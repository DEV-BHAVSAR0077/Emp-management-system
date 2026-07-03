<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\SendUserFinancialReportJob;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendScheduledReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find users due for financial reports and dispatch queued jobs for each.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting SendScheduledReportsCommand...');
        Log::info('Scheduler: Command reports:send started.');

        $now = Carbon::now();

        // Find users with a set frequency whose next_send_at is due.
        // We use chunkById for performance over large datasets to prevent memory exhaustion.
        User::whereNotNull('report_frequency')
            ->where('next_send_at', '<=', $now)
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    // Temporarily advance next_send_at by a small margin (e.g. 1 hour)
                    // so that the scheduler (running every minute) does not re-dispatch 
                    // this same user if the queue worker is slow.
                    // The job itself will set the actual next_send_at once successful.
                    $user->next_send_at = Carbon::now()->addHour();
                    $user->save();

                    // Dispatch job to the queue
                    SendUserFinancialReportJob::dispatch($user);
                    
                    $this->info("Dispatched report job for user: {$user->email}");
                    Log::info("Scheduler: Dispatched SendUserFinancialReportJob for user {$user->id}");
                }
            });

        $this->info('Scheduler Command Completed.');
        Log::info('Scheduler: Command reports:send completed.');
        
        return self::SUCCESS;
    }
}
