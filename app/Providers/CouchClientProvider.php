<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CouchClientProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // TODO register CouchClient from php-on-couch
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
