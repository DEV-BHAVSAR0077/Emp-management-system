<?php

namespace App\Http\Controllers;

use App\Models\AgencyVendor;
use App\Http\Requests\AVStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        $agencyVendors = AgencyVendor::query()
            ->withSum('expenses', 'amount')
            ->withSum('payments', 'amount')
            ->when($avSearch, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10, ['*'], 'av_page');

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
}
