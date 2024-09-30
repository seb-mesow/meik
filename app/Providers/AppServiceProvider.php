<?php
declare(strict_types=1);

namespace App\Providers;

use App\CouchDBUserProvider;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
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
		Auth::provider('couchdb', callback: static function(Application $app, array $config): UserProvider {
			return $app->make(CouchDBUserProvider::class);
		});
		
        Vite::prefetch(concurrency: 3);
    }
}
