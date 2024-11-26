<?php
declare(strict_types=1);

namespace App\Providers;

use App\Repository\CouchDBUserProvider;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		$this->app->singleton(SerializerBuilder::class, static function() {
			return SerializerBuilder::create();
		});
		$this->app->singleton(Serializer::class, static function(Application $app) {
			return $app->make(SerializerBuilder::class)->build();
		});
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		Auth::provider('couchdb', static function(Application $app, array $config): UserProvider {
			return $app->make(CouchDBUserProvider::class);
		});
		Auth::extend('couchdb', static function(Application $app, string $guard_name, array $config) {
			$guard = new SessionGuard(
				'session',
				Auth::createUserProvider($config['provider']),
				$app->make('session.store'),
				$app->make('request')
			);
			if (method_exists($guard, 'setCookieJar')) {
				$guard->setCookieJar($app->make('cookie'));
			}
			return $guard;
		});
		
		Vite::prefetch(concurrency: 3);
	}
}
