# Production Email Scheduler Architecture

## 1. Files Created
- `c:\wamp64\www\dev\database\migrations\2026_07_03_092711_add_report_fields_to_users_table.php` (Migration to add fields to users table)
- `c:\wamp64\www\dev\app\Console\Commands\SendScheduledReportsCommand.php` (The command executed by the scheduler every minute)
- `c:\wamp64\www\dev\app\Jobs\SendUserFinancialReportJob.php` (The queued Job that handles email generation and dispatch)

## 2. Files Modified
- `c:\wamp64\www\dev\app\Models\User.php` (Added `$fillable` fields and `$casts`)
- `c:\wamp64\www\dev\app\Http\Controllers\ProfileController.php` (Added logic to process `report_frequency` and calculate the initial `next_send_at`)
- `c:\wamp64\www\dev\resources\views\profile\edit.blade.php` (Added dropdown for users to select their report frequency)
- `c:\wamp64\www\dev\routes\console.php` (Modified the scheduler to execute `reports:send` every minute instead of the old command)

## 3. Database Migrations
A migration was added to the `users` table because a key-value settings table performs very poorly when querying thousands of users for `where('next_send_at', '<=', now())`. The `users` table gained:
- `report_frequency` (string/enum: daily, weekly, monthly, nullable)
- `next_send_at` (timestamp, nullable, indexed for performance)
- `last_sent_at` (timestamp, nullable)

## 4. Commands to Execute
Run the following on your server to complete setup:
```bash
# Migrate the database to add the new columns
php artisan migrate

# If not already done, ensure the jobs table exists for queues
php artisan queue:table
php artisan migrate

# Start the queue worker (this must stay running)
php artisan queue:work

# Test the scheduler locally
php artisan schedule:work
```

## 5. How to Test Manually
1. Go to your Profile settings in the UI and select "Daily" for your frequency. Save the profile.
2. Open your database or Tinker and manually change your `next_send_at` to a time in the past:
   `User::find(your_id)->update(['next_send_at' => now()->subMinute()]);`
3. Run `php artisan reports:send`. You should see output indicating it dispatched a job.
4. Ensure `php artisan queue:work` is running in another terminal. You will see it process `SendUserFinancialReportJob`.
5. Check your email. Check your database to see `last_sent_at` populated and `next_send_at` moved forward by 1 day.

## 6. Expected Outputs
- The scheduler runs every minute. Most minutes it does nothing if no users are due.
- When users are due, the Command dispatches Jobs and temporarily advances their `next_send_at` to prevent double-dispatch.
- The Queue worker receives the Job, generates the PDF report for the correct date range, sends the email, and then sets the real `next_send_at`.

## 7. Possible Failure Scenarios
- **Job Failure**: If the PDF generation or Email SMTP fails, the job fails. The user's `next_send_at` was temporarily pushed forward by 1 hour by the Command. The queue system will retry the job up to 3 times (configured in the Job). If it completely fails, you can see it in the `failed_jobs` table.
- **Queue Worker Down**: If `php artisan queue:work` is not running, the scheduler will dispatch jobs, but they will pile up in the `jobs` table until the worker starts.

## 8. How to Debug
- Check `storage/logs/laravel.log`. The Command and Job both write structured logs (e.g. `Scheduler: Command reports:send started.`, `Sent daily financial report to user...`).
- Check the `failed_jobs` table in the database if emails are not arriving. Use `php artisan queue:failed`.
- Use `php artisan queue:retry all` to retry failed jobs.

## 9. How to Deploy to Production
- Pull code.
- Run `php artisan migrate`.
- Configure `Supervisor` to keep `php artisan queue:work` running continuously in the background.
- Configure Cron (Linux) or Task Scheduler (Windows) to run `php artisan schedule:run` every minute.

## 10. How to Configure Linux Cron
Run `crontab -e` and add this line:
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 11. How to Configure Windows Task Scheduler
If you host this on Windows:
1. Open **Task Scheduler**.
2. Click **Create Task...**.
3. **General Tab**: Name it "Laravel Scheduler". Check "Run whether user is logged on or not".
4. **Triggers Tab**: New > Begin the task: On a schedule. Choose "Daily". Under Advanced settings, check "Repeat task every: 1 minute" for a duration of "Indefinitely".
5. **Actions Tab**: New > Action: Start a program.
   - Program/script: `php` (or the full path to `php.exe`, e.g. `C:\wamp64\bin\php\php8.1\php.exe`)
   - Add arguments: `artisan schedule:run`
   - Start in: `C:\wamp64\www\dev`
6. Click OK and save the task.
