<?php

namespace App\Providers;

use App\Models\Expense;
use App\Observers\ExpenseObserver;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observer to auto-sync agency/vendor balance on expense changes
        Expense::observe(ExpenseObserver::class);

        Gate::before(function ($user, $ability) {
            if ($user->hasPermission($ability)) {
                return true;
            }
        });
    }
}
