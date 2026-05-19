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
            new Middleware('permission:create-agency-vendor', only: ['create', 'store']),
            new Middleware('permission:edit-agency-vendor', only: ['edit', 'update']),
            new Middleware('permission:delete-agency-vendor', only: ['destroy']),
        ];
    }

    public function index()
    {
        return redirect()->route('dashboard', ['tab' => 'agency_vendors']);
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

        return redirect()->route('dashboard', ['tab' => 'agency_vendors'])
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

        return redirect()->route('dashboard', ['tab' => 'agency_vendors'])
                         ->with('success', 'Agency/Vendor updated successfully.');
    }

    public function destroy(AgencyVendor $agencyVendor)
    {
        $agencyVendor->delete();

        return back()->with('success', 'Agency/Vendor deleted successfully.');
    }
}
