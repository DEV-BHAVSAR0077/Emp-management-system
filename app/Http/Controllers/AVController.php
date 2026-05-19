<?php

namespace App\Http\Controllers;

use App\Models\AgencyVendor;
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

    private function validationRules(): array
    {
        return [
            'name'           => 'required|string|max:150',
            'type'           => 'required|string|in:Agency,Vendor',
            'email'          => 'nullable|email|max:150',
            'phone_number'   => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:150',
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

    public function store(Request $request)
    {
        $request->validate($this->validationRules());

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

    public function update(Request $request, AgencyVendor $agencyVendor)
    {
        $request->validate($this->validationRules());

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
