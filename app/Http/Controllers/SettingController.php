<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    // Display the settings page
    public function index()
    {
        // Get all settings as a key-value array
        $settings = Setting::pluck('value', 'key')->toArray();
        $roles = Role::orderBy('name')->get();

        return view('settings.index', [
            'user' => Auth::user(),
            'settings' => $settings,
            'roles' => $roles,
        ]);
    }

    // Update settings
    public function store(Request $request)
    {
        // Handle weekly report roles
        $rolesSelected = $request->input('weekly_report_roles', []);
        
        Setting::updateOrCreate(
            ['key' => 'weekly_report_roles'],
            ['value' => json_encode($rolesSelected)]
        );

        // Handle master toggle for weekly report
        $weeklyReportEnabled = $request->boolean('weekly_report_enabled');
        Setting::updateOrCreate(
            ['key' => 'weekly_report_enabled'],
            ['value' => $weeklyReportEnabled]
        );

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
