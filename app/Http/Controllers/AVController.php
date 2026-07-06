<?php

namespace App\Http\Controllers;

use App\Models\AgencyVendor;
use App\Models\VendorLedger;
use \App\Models\Expense;
use \App\Models\Payment;
use App\Http\Requests\AVStoreRequest;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
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


    public function index(Request $request)
    {
        $avSearch = $request->input('av_search', '');
        
        $agencyVendors = DB::table('agency_vendors')
            ->select('agency_vendors.*')
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


    private function getLedgerQuery(Request $request, AgencyVendor $agencyVendor)
    {
        $query = VendorLedger::where('vendor_id', $agencyVendor->id)
            ->with('loggable');

        if ($request->filled('sort_order') && in_array(strtolower($request->sort_order), ['asc', 'desc'])) {
            $direction = strtolower($request->sort_order);
            $query->orderBy('log_at', $direction)
                  ->orderBy('id', $direction);
        } else {
            // Default Database Order
            $query->orderBy('timestamp', 'desc')
                  ->orderBy('id', 'desc');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('log_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('log_at', '<=', $request->end_date);
        }

        return $query;
    }

    private function formatLedgerItem($item)
    {
        $date = Carbon::parse($item->log_at);
        $typeLabel = '';
        if ($item->loggable_type === Expense::class) {
            $typeLabel = 'Expense';
        } elseif ($item->loggable_type === Payment::class) {
            $typeLabel = 'Payment';
        } else {
            $typeLabel = class_basename($item->loggable_type);
        }

        return [
            'id' => $item->id,
            'date_formatted' => $date->format('d M Y'),
            'type_label' => $typeLabel,
            'debit_formatted' => $item->debit > 0 ? number_format($item->debit, 2) : '-',
            'credit_formatted' => $item->credit > 0 ? number_format($item->credit, 2) : '-',
            'balance_formatted' => $item->balance < 0 ? '-₹' . number_format(abs($item->balance), 2) : '₹' . number_format($item->balance, 2),
            'system_note' => $item->system_note,
        ];
    }

    public function getVendorLedger(Request $request, AgencyVendor $agencyVendor)
    {
        $ledgers = $this->getLedgerQuery($request, $agencyVendor)->paginate(15);

        // Format dates and amounts for JSON response
        $ledgers->getCollection()->transform(function ($item) {
            return $this->formatLedgerItem($item);
        });

        return response()->json([
            'ledgers' => $ledgers->items(),
            'pagination' => [
                'current_page' => $ledgers->currentPage(),
                'last_page' => $ledgers->lastPage(),
                'total' => $ledgers->total(),
            ],
            'agency_vendor_name' => $agencyVendor->name,
            'final_balance' => (float) $agencyVendor->balance
        ]);
    }

    public function downloadLedgerCsv(Request $request, AgencyVendor $agencyVendor)
    {
        $ledgers = $this->getLedgerQuery($request, $agencyVendor)->get();
        
        $filename = "ledger_history_{$agencyVendor->name}_" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Date', 'Transaction', 'Debit', 'Credit', 'Balance', 'Details'];

        $callback = function() use($ledgers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($ledgers as $ledger) {
                $formatted = $this->formatLedgerItem($ledger);
                $row = [
                    $formatted['date_formatted'],
                    $formatted['type_label'],
                    $formatted['debit_formatted'],
                    $formatted['credit_formatted'],
                    $formatted['balance_formatted'],
                    $formatted['system_note']
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadLedgerPdf(Request $request, AgencyVendor $agencyVendor)
    {
        $ledgers = $this->getLedgerQuery($request, $agencyVendor)->get();
        
        $formattedLedgers = $ledgers->map(function($item) {
            return $this->formatLedgerItem($item);
        });

        $pdf = Pdf::loadView('agency_vendors.ledger_pdf', [
            'agencyVendor' => $agencyVendor,
            'ledgers' => $formattedLedgers,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
        ]);

        $filename = "ledger_history_{$agencyVendor->name}_" . date('Y-m-d') . ".pdf";
        return $pdf->download($filename);
    }
}
