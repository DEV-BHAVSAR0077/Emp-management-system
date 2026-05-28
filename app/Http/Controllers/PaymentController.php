<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\AgencyVendor;
use App\Models\Expense;
use App\Models\Payment;
use App\Services\SyncBalance;
use App\Services\VendorLedgerService;
use Maatwebsite\Excel\Facades;
use Maatwebsite\Excel\Concerns;
use App\Imports;
use Illuminate\Validation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PaymentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-payment', only: ['index']),
            new Middleware('permission:create-payment', only: ['create', 'store']),
            new Middleware('permission:edit-payment', only: ['edit', 'update']),
            new Middleware('permission:delete-payment', only: ['destroy']),
        ];
    }

    /**
     * Display the payments list.
     */
    public function index(Request $request)
    {
        $paymentSearch = $request->input('payment_search', '');
        $paymentStartDate = $request->input('payment_start_date', '');
        $paymentEndDate = $request->input('payment_end_date', '');

        $payments = Payment::with(['agencyVendor', 'user'])
            ->when($paymentSearch, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('notes', 'like', "%{$search}%")
                      ->orWhereHas('agencyVendor', function ($avq) use ($search) {
                          $avq->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($paymentStartDate, function ($query, $start) {
                $query->whereDate('payment_date', '>=', $start);
            })
            ->when($paymentEndDate, function ($query, $end) {
                $query->whereDate('payment_date', '<=', $end);
            })
            ->orderBy('id', 'desc')
            ->paginate(8, ['*'], 'payment_page');

        $agencyVendors = AgencyVendor::select('agency_vendors.*')
            ->withSum('expenses', 'amount')
            ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN payment_type = 1 THEN amount WHEN payment_type = 0 THEN -amount ELSE amount END), 0) FROM payments WHERE payments.agency_vendor_id = agency_vendors.id AND payments.deleted_at IS NULL) as payments_sum_amount')
            ->orderBy('name')
            ->get();

        return view('payments.index', [
            'user'          => Auth::user(),
            'payments'         => $payments,
            'paymentSearch'    => $paymentSearch,
            'paymentStartDate' => $paymentStartDate,
            'paymentEndDate'   => $paymentEndDate,
            'agencyVendors'    => $agencyVendors,
        ]);
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Request $request)
    {
        $agencyVendors = AgencyVendor::orderBy('name')->get();

        return view('payments.create', [
            'user'          => Auth::user(),
            'agencyVendors' => $agencyVendors,
        ]);
    }

    /**
     * Store a newly created payment.
     */
    public function store(PaymentRequest $request)
    {
        $agencyVendorId = $request->agency_vendor_id;
        $paymentNotes = $request->notes ? mb_substr(trim($request->notes), 0, 1000) : null;

        $duplicateExists = Payment::where('user_id', Auth::id())
            ->where('agency_vendor_id', $agencyVendorId)
            ->where('amount', $request->amount)
            ->where('payment_type', $request->payment_type)
            ->where('payment_date', $request->payment_date)
            ->where('notes', $paymentNotes)
            ->exists();

        if ($duplicateExists) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'An exact duplicate of this payment already exists in the system.',
                    'errors' => ['agency_vendor_id' => ['An exact duplicate of this payment already exists in the system.']]
                ], 422);
            }
            return back()->withInput()->with('error', 'An exact duplicate of this payment already exists in the system.');
        }

        $payment = Payment::create([
            'user_id'          => Auth::id(),
            'agency_vendor_id' => $agencyVendorId,
            'amount'           => $request->amount,
            'payment_type'     => $request->payment_type,
            'notes'            => $paymentNotes,
            'payment_date'     => $request->payment_date,
        ]);

        $newBalance = SyncBalance::updateBalance($payment->agency_vendor_id, $payment->amount, 'payment', 'add', $payment->payment_type);
        if ($payment->agency_vendor_id) {
            VendorLedgerService::addEntry($payment, $payment->agency_vendor_id, $payment->amount, 'payment', $newBalance, 'Payment Added', $payment->payment_type);
        }

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'Payment recorded successfully.');
            return response()->json(['success' => true, 'message' => 'Payment recorded successfully.']);
        }

        return redirect()->route('payments.index')
                         ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        $agencyVendors = AgencyVendor::orderBy('name')->get();

        return view('payments.edit', [
            'user'          => Auth::user(),
            'payment'       => $payment,
            'agencyVendors' => $agencyVendors,
        ]);
    }

    /**
     * Update the specified payment.
     */
    public function update(PaymentRequest $request, Payment $payment)
    {
        $oldVendorId = $payment->agency_vendor_id;
        $oldAmount = $payment->amount;
        $oldPaymentType = $payment->payment_type;

        $newVendorId = $request->agency_vendor_id;
        $newAmount = $request->amount;
        $newPaymentType = $request->payment_type;

        $oldBalance = SyncBalance::updateBalance($oldVendorId, $oldAmount, 'payment', 'remove', $oldPaymentType);
        if ($oldVendorId && $oldVendorId != $newVendorId) {
            VendorLedgerService::addRemoveEntry($payment, $oldVendorId, $oldAmount, 'payment', $oldBalance, 'Payment Deleted (Vendor Changed)', $oldPaymentType);
        }

        $payment->update([
            'agency_vendor_id' => $newVendorId,
            'amount'           => $newAmount,
            'payment_type'     => $newPaymentType,
            'notes'            => $request->notes,
            'payment_date'     => $request->payment_date,
        ]);

        $newBalance = SyncBalance::updateBalance($newVendorId, $newAmount, 'payment', 'add', $newPaymentType);
        if ($newVendorId) {
            if ($oldVendorId == $newVendorId) {
                VendorLedgerService::addUpdateEntry(
                    $payment, 
                    $newVendorId, 
                    $oldAmount, 'payment', $oldPaymentType, 
                    $newAmount, 'payment', $newPaymentType, 
                    $newBalance, 'Payment Updated'
                );
            } else {
                VendorLedgerService::addEntry($payment, $newVendorId, $newAmount, 'payment', $newBalance, 'Payment Added (Vendor Changed)', $newPaymentType);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'Payment updated successfully.');
            return response()->json(['success' => true, 'message' => 'Payment updated successfully.']);
        }

        return redirect()->route('payments.index')
                         ->with('success', 'Payment updated successfully.');
    }

    /**
     * Soft-delete the specified payment.
     */
    public function destroy(Payment $payment)
    {
        $newBalance = SyncBalance::updateBalance($payment->agency_vendor_id, $payment->amount, 'payment', 'remove', $payment->payment_type);
        if ($payment->agency_vendor_id) {
            VendorLedgerService::addRemoveEntry($payment, $payment->agency_vendor_id, $payment->amount, 'payment', $newBalance, 'Payment Deleted', $payment->payment_type);
        }
        $payment->delete();

        return back()->with('success', 'Payment deleted successfully.');
    }

    // ---------------------------------------------------------
    // Excel Import & Template
    // ---------------------------------------------------------

    public function downloadTemplate()
    {
        return Excel::download(new class implements FromArray, WithHeadings {
            public function headings(): array {
                return [
                    'agency_vendor',
                    'amount',
                    'payment_type',
                    'payment_date',
                    'notes'
                ];
            }
            public function array(): array {
                return [[
                    'Amazon',
                    '500.00',
                    'Credit',
                    date('Y-m-d'),
                    'Advance payment for bulk order'
                ]];
            }
        }, 'payment_template.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'payment_excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'payment_excel_file.required' => 'Please select an Excel file to upload.',
            'payment_excel_file.mimes'    => 'The file must be a valid Excel (.xlsx, .xls) or CSV file.',
            'payment_excel_file.max'      => 'The file size must not exceed 10MB.'
        ]);

        try {
            Excel::import(new PaymentsImport, $request->file('payment_excel_file'));
            return redirect()->route('payments.index')->with('success', 'Payments imported successfully!');
        } catch (ValidationException $e) {
            $errorMsgs = collect($e->errors())->flatten()->toArray();
            return redirect()->route('payments.index')->withErrors(['payment_import_errors' => $errorMsgs]);
        } catch (\Exception $e) {
            return redirect()->route('payments.index')->withErrors(['payment_import_errors' => [$e->getMessage()]]);
        }
    }
}
