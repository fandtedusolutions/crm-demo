<?php

namespace App\Providers;

use App\Models\ConvertedLead;
use App\Observers\ConvertedLeadObserver;
use Illuminate\Support\ServiceProvider;

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
        // Load helper functions
        require_once app_path('helpers.php');

        ConvertedLead::observe(ConvertedLeadObserver::class);
    }
}
