<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\User;
use App\Models\Expense;
use App\Models\Payment;
use App\Mail\FinancialReportMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SendFinancialReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:financial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send financial report to selected roles based on configured frequency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting financial report generation...');

        $enabledSetting = Setting::where('key', 'weekly_report_enabled')->first();
        if (!$enabledSetting || $enabledSetting->value !== '1') {
            $this->info('Financial report feature is currently disabled. Exiting.');
            return self::SUCCESS;
        }

        $frequencySetting = Setting::where('key', 'financial_report_frequency')->first();
        $frequency = $frequencySetting ? $frequencySetting->value : 'weekly';

        $now = Carbon::now();

        // Check if it's the correct day to run based on frequency
        if ($frequency === 'weekly' && !$now->isMonday()) {
            $this->info('Frequency is weekly, but today is not Monday. Exiting.');
            return self::SUCCESS;
        }

        if ($frequency === 'monthly' && !$now->isLastOfMonth()) {
            $this->info('Frequency is monthly, but today is not the last day of the month. Exiting.');
            return self::SUCCESS;
        }

        $settingRoles = Setting::where('key', 'weekly_report_roles')->first();
        if (!$settingRoles || empty($settingRoles->value)) {
            $this->info('No roles selected for financial report. Exiting.');
            return self::SUCCESS;
        }

        $roles = json_decode($settingRoles->value, true) ?? [];
        if (empty($roles)) {
            $this->info('No roles selected for financial report. Exiting.');
            return self::SUCCESS;
        }

        // Get users with the selected roles
        $users = User::query()->select(['email'])->whereIn('role', $roles)->get();
        if ($users->isEmpty()) {
            $this->info('No users found with the selected roles. Exiting.');
            return self::SUCCESS;
        }

        // Determine date range based on frequency
        if ($frequency === 'daily') {
            $startDate = $now->copy()->startOfDay();
            $endDate = $now->copy()->endOfDay();
        } elseif ($frequency === 'weekly') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
        } else { // monthly
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
        }

        $startStr = $startDate->toDateString();
        $endStr = $endDate->format('Y-m-d');

        // Calculate totals
        $expenses = Expense::whereBetween('expense_date', [$startStr, $endStr])->sum('amount');
        $payments = Payment::whereBetween('payment_date', [$startStr, $endStr])->sum('amount');
        
        $finalAmount = $payments - $expenses;

        // Details for PDF
        $expensesList = Expense::with('category')->whereBetween('expense_date', [$startStr, $endStr])->get();
        $paymentsList = Payment::with('agencyVendor')->whereBetween('payment_date', [$startStr, $endStr])->get();

        $entries = collect();
        
        foreach ($expensesList as $exp) {
            $entries->push([
                'date' => $exp->expense_date->format('Y-m-d'),
                'type' => 'Expense',
                'description' => $exp->category ? $exp->category->name : ($exp->name ?? 'N/A'),
                'amount' => $exp->amount,
                'timestamp' => $exp->expense_date->timestamp,
            ]);
        }
        
        foreach ($paymentsList as $pay) {
            $desc = $pay->agencyVendor ? $pay->agencyVendor->name : 'N/A';
            if ($pay->notes) {
                $desc .= ' (' . $pay->notes . ')';
            }
            $entries->push([
                'date' => $pay->payment_date->format('Y-m-d'),
                'type' => 'Payment',
                'description' => $desc,
                'amount' => $pay->amount,
                'timestamp' => $pay->payment_date->timestamp,
            ]);
        }

        // Sort by timestamp
        $entries = $entries->sortBy('timestamp')->values()->all();

        $pdfData = [
            'frequency' => $frequency,
            'startDate' => $startDate->format('M d, Y'),
            'endDate' => $endDate->format('M d, Y'),
            'entries' => $entries,
            'expenses' => $expenses,
            'payments' => $payments,
            'finalAmount' => $finalAmount,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.financial_report_pdf', $pdfData);
        $pdfContent = $pdf->output();

        $reportData = [
            'frequency' => $frequency,
            'start_date' => $startDate->format('M d, Y'),
            'end_date' => $endDate->format('M d, Y'),
            'expenses' => $expenses,
            'payments' => $payments,
            'final_amount' => $finalAmount,
        ];

        Mail::to($users->pluck('email')->toArray())->send(new FinancialReportMail($reportData, $pdfContent));

        $this->info(ucfirst($frequency) . ' financial report sent successfully to ' . $users->count() . ' users.');
        
        return self::SUCCESS;
    }
}
