<?php

namespace App\Http\Controllers;

use App\Models\AgencyVendor;
use App\Http\Requests\AVStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AVController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-agency-vendor', only: ['index']),
            new Middleware('permission:create-agency-vendor', only: ['create', 'store']),
            new Middleware('permission:edit-agency-vendor', only: ['edit', 'update']),
            new Middleware('permission:delete-agency-vendor', only: ['destroy']),
        ];
    }

    // public function index(Request $request)
    // {
    //     $avSearch = $request->input('av_search', '');
        
    //     $agencyVendors = AgencyVendor::query()
    //         ->withSum('expenses', 'amount')
    //         ->withSum('payments', 'amount')
    //         ->when($avSearch, function ($query, $search) {
    //             $query->where('name', 'like', "%{$search}%")
    //                   ->orWhere('email', 'like', "%{$search}%")
    //                   ->orWhere('contact_person', 'like', "%{$search}%");
    //         })
    //         ->orderBy('name')
    //         ->paginate(10, ['*'], 'av_page');

    //     return view('agency_vendors.index', [
    //         'agencyVendors' => $agencyVendors,
    //         'avSearch'      => $avSearch,
    //     ]);
    // }

    public function index(Request $request)
    {
        $avSearch = $request->input('av_search', '');
        
        $agencyVendors = DB::table('agency_vendors')
            ->select('agency_vendors.*')
            ->selectRaw('(SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE expenses.agency_vendor_id = agency_vendors.id AND expenses.deleted_at IS NULL) as expenses_sum_amount')
            ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN payment_type = 1 THEN amount WHEN payment_type = 0 THEN -amount ELSE amount END), 0) FROM payments WHERE payments.agency_vendor_id = agency_vendors.id AND payments.deleted_at IS NULL) as payments_sum_amount')
            ->whereNull('agency_vendors.deleted_at')
            ->when($avSearch, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('agency_vendors.name', 'like', "%{$search}%")
                      ->orWhere('agency_vendors.email', 'like', "%{$search}%")
                      ->orWhere('agency_vendors.contact_person', 'like', "%{$search}%");
                });
            })
            ->orderBy('agency_vendors.name')
            ->paginate(6, ['*'], 'av_page');

        return view('agency_vendors.index', [
            'agencyVendors' => $agencyVendors,
            'avSearch'      => $avSearch,
        ]);
    }

    public function create()
    {
        return view('agency_vendors.create', [
            'user' => Auth::user(),
        ]);
    }

    public function store(AVStoreRequest $request)
    {

        AgencyVendor::create($request->only([
            'name', 'type', 'email', 'phone_number', 'contact_person'
        ]));

        return redirect()->route('agency_vendors.index')
                         ->with('success', 'Agency/Vendor created successfully.');
    }

    public function edit(AgencyVendor $agencyVendor)
    {
        return view('agency_vendors.edit', [
            'user' => Auth::user(),
            'agencyVendor' => $agencyVendor,
        ]);
    }

    public function update(AVStoreRequest $request, AgencyVendor $agencyVendor)
     {

        $agencyVendor->update($request->only([
            'name', 'type', 'email', 'phone_number', 'contact_person'
        ]));

        return redirect()->route('agency_vendors.index')
                         ->with('success', 'Agency/Vendor updated successfully.');
    }

    public function destroy(AgencyVendor $agencyVendor)
    {
        $agencyVendor->delete();

        return back()->with('success', 'Agency/Vendor deleted successfully.');
    }

    public function getPayments(Request $request, AgencyVendor $agencyVendor)
    {
        $payments = \App\Models\Payment::where('agency_vendor_id', $agencyVendor->id)
            ->orderBy('payment_date', 'desc')
            ->paginate(8);

        // Format dates and amounts for JSON response
        $payments->getCollection()->transform(function ($payment) {
            return [
                'id' => $payment->id,
                'amount_formatted' => number_format($payment->amount, 2),
                'payment_type' => $payment->payment_type,
                'payment_type_label' => \App\Models\Payment::TYPES[$payment->payment_type] ?? ucfirst($payment->payment_type),
                'date_formatted' => $payment->payment_date->format('d M Y'),
                'notes' => $payment->notes
            ];
        });

        return response()->json([
            'payments' => $payments->items(),
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'total' => $payments->total(),
            ],
            'agency_vendor_name' => $agencyVendor->name
        ]);
    }
}
