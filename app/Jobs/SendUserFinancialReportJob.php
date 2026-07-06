<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Expense;
use App\Models\Payment;
use App\Mail\FinancialReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SendUserFinancialReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    
    // Retry configurations
    public $tries = 3;
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $frequency = $this->user->report_frequency;

        if (!$frequency) {
            Log::info("Skipping report for user {$this->user->id}: No frequency selected.");
            return;
        }

        $now = Carbon::now();

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
        $expensesBase = Expense::whereBetween('expense_date', [$startStr, $endStr])->sum('amount');
        
        $paymentsDebit = Payment::where('payment_type', Payment::TYPE_DEBIT)
                                ->whereBetween('payment_date', [$startStr, $endStr])
                                ->sum('amount');
                                
        $paymentsCredit = Payment::where('payment_type', Payment::TYPE_CREDIT)
                                 ->whereBetween('payment_date', [$startStr, $endStr])
                                 ->sum('amount');
        
        $expenses = $expensesBase + $paymentsCredit;
        $payments = $paymentsDebit;
        
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
            $paymentTypeString = $pay->payment_type == Payment::TYPE_CREDIT ? 'Credit' : 'Debit';
            $entries->push([
                'date' => $pay->payment_date->format('Y-m-d'),
                'type' => 'Payment',
                'sub_type' => $paymentTypeString,
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

        // Send Email
        Mail::to($this->user->email)->send(new FinancialReportMail($reportData, $pdfContent));
        
        Log::info("Sent {$frequency} financial report to user {$this->user->id} ({$this->user->email}).");

        // Update timestamps according to selected frequency
        $this->user->last_sent_at = Carbon::now();
        $this->user->next_send_at = match ($frequency) {
            'daily'   => Carbon::now()->endOfDay()->subMinutes(5), // 23:55 today or tomorrow if already past
            'weekly'  => Carbon::now()->endOfWeek()->subMinutes(5), // 23:55 end of week
            'monthly' => Carbon::now()->endOfMonth()->subMinutes(5), // 23:55 end of month
        };
        
        // If the calculated next_send_at is in the past (e.g. we manually triggered it late), advance to the next period
        if ($this->user->next_send_at <= Carbon::now()) {
            $this->user->next_send_at = match ($frequency) {
                'daily'   => Carbon::now()->addDay()->endOfDay()->subMinutes(5),
                'weekly'  => Carbon::now()->addWeek()->endOfWeek()->subMinutes(5),
                'monthly' => Carbon::now()->addMonth()->endOfMonth()->subMinutes(5),
            };
        }
        $this->user->save();
    }
    
    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to send financial report to user {$this->user->id}: " . $exception->getMessage());
    }
}
