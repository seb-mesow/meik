<?php
declare(strict_types=1);

namespace App\Providers;

use App\Repository\CouchDBUserProvider;
use App\Repository\LocationRepository;
use App\Repository\PlaceRepository;
use Database\Seeders\ExhibitSeeder;
use Database\Seeders\ImageSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\PlaceSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use View;

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
		
		$this->app->singleton(LocationRepository::class);
		$this->app->singleton(PlaceRepository::class);
		
		$this->app->singleton(UserSeeder::class);
		$this->app->singleton(LocationSeeder::class);
		$this->app->singleton(PlaceSeeder::class);
		$this->app->singleton(ExhibitSeeder::class);
		$this->app->singleton(ImageSeeder::class);
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
		
		View::addNamespace('errors', resource_path('views/errors'));
		
		Vite::prefetch(concurrency: 3);
	}
}
