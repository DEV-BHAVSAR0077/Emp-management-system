<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Setting;
use App\Models\Role;
use App\Jobs\SendUserFinancialReportJob;

$settingRoles = Setting::where('key', 'weekly_report_roles')->first();
$roles = json_decode($settingRoles->value, true) ?? [];

if (empty($roles)) {
    echo "No roles selected in global settings.\n";
    exit;
}

$roleIds = Role::whereIn('name', $roles)->pluck('id');
$users = User::whereIn('role_id', $roleIds)->get();

if ($users->isEmpty()) {
    echo "No users found with the selected roles.\n";
    exit;
}

// Get the global frequency setting as a fallback
$frequencySetting = Setting::where('key', 'financial_report_frequency')->first();
$globalFrequency = $frequencySetting ? $frequencySetting->value : 'monthly';

echo "Found {$users->count()} users in roles: " . implode(', ', $roles) . "\n";
echo "Global fallback frequency is: {$globalFrequency}\n";

foreach ($users as $user) {
    // Force their personal frequency to match the global frequency setting
    $user->report_frequency = $globalFrequency;
    $user->save();
    
    SendUserFinancialReportJob::dispatch($user);
    echo "Force-dispatched email job for user: {$user->email} (Frequency: {$user->report_frequency})\n";
}

echo "Done! The queue worker will process these immediately.\n";
