<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\User;
use App\Models\Expense;
use App\Models\Payment;
use App\Mail\WeeklyFinancialReport;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly financial report to selected roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting weekly report generation...');

        $enabledSetting = Setting::where('key', 'weekly_report_enabled')->first();
        if (!$enabledSetting || $enabledSetting->value !== '1') {
            $this->info('Weekly report feature is currently disabled. Exiting.');
            return;
        }

        $setting = Setting::where('key', 'weekly_report_roles')->first();
        if (!$setting || empty($setting->value)) {
            $this->info('No roles selected for weekly report. Exiting.');
            return;
        }

        $roles = json_decode($setting->value, true) ?? [];
        if (empty($roles)) {
            $this->info('No roles selected for weekly report. Exiting.');
            return;
        }

        // Get users with the selected roles
        $users = User::query()->select(['email'])->whereIn('role', $roles)->get();
        if ($users->isEmpty()) {
            $this->info('No users found with the selected roles. Exiting.');
            return;
        }

        $startDate = Carbon::now()->startOfWeek(); // Start of the current week (Monday)
        $endDate = Carbon::now()->endOfWeek(); // End of the current week (Sunday)

        // Calculate totals
        $expenses = Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->format('Y-m-d')])->sum('amount');
        
        // Payments table uses payment_date or created_at? Let's assume created_at for simplicity, 
        // wait, earlier we saw: $query->whereDate('payment_date', '>=', $start);
        $payments = Payment::whereBetween('payment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->sum('amount');

        $finalAmount = $payments - $expenses;

        $reportData = [
            'start_date' => $startDate->format('M d, Y'),
            'end_date' => $endDate->format('M d, Y'),
            'expenses' => $expenses,
            'payments' => $payments,
            'final_amount' => $finalAmount,
        ];

        Mail::to($users->pluck('email')->toArray())->send(new WeeklyFinancialReport($reportData));

        // foreach ($users as $user) {
        //     $this->info("Sending email to {$user->email}...");
        //     Mail::to($user->email)->send(new WeeklyFinancialReport($reportData));
        // }

        $this->info('Weekly report sent successfully.');
        self::SUCCESS;
    }
}
