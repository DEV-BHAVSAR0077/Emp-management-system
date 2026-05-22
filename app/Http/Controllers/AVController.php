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
        $expenses = DB::table('expenses')
            ->select('id', 'amount', DB::raw("'expense' as record_type"), DB::raw('NULL as payment_type'), 'expense_date as record_date', 'note as notes', 'created_at')
            ->where('agency_vendor_id', $agencyVendor->id)
            ->whereNull('deleted_at');

        $payments = DB::table('payments')
            ->select('id', 'amount', DB::raw("'payment' as record_type"), 'payment_type', 'payment_date as record_date', 'notes', 'created_at')
            ->where('agency_vendor_id', $agencyVendor->id)
            ->whereNull('deleted_at');

        $history = $expenses->unionAll($payments)
            ->orderBy('record_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        // Format dates and amounts for JSON response
        $history->getCollection()->transform(function ($item) {
            $date = \Carbon\Carbon::parse($item->record_date);
            
            $typeLabel = '';
            $color = '';
            $badgeBg = '';
            $badgeColor = '';
            
            if ($item->record_type === 'expense') {
                $typeLabel = 'Expense';
                $color = 'var(--text)'; // Default text color
                $badgeBg = '#e0e7ff'; // Indigo bg
                $badgeColor = '#3730a3'; // Indigo text
            } else {
                if ($item->payment_type == 1) { // Credit
                    $typeLabel = 'Credit';
                    $color = 'var(--success)';
                    $badgeBg = '#dcfce7';
                    $badgeColor = '#166534';
                } else { // Debit
                    $typeLabel = 'Debit';
                    $color = 'var(--danger)';
                    $badgeBg = '#fee2e2';
                    $badgeColor = '#991b1b';
                }
            }

            return [
                'id' => $item->id,
                'amount_formatted' => number_format($item->amount, 2),
                'type_label' => $typeLabel,
                'date_formatted' => $date->format('d M Y'),
                'notes' => $item->notes,
                'color' => $color,
                'badge_bg' => $badgeBg,
                'badge_color' => $badgeColor
            ];
        });

        return response()->json([
            'payments' => $history->items(),
            'pagination' => [
                'current_page' => $history->currentPage(),
                'last_page' => $history->lastPage(),
                'total' => $history->total(),
            ],
            'agency_vendor_name' => $agencyVendor->name,
            'final_balance' => (float) $agencyVendor->balance
        ]);
    }
}
