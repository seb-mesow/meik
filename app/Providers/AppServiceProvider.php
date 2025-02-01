<?php
declare(strict_types=1);

namespace App\Providers;

use App\Repository\CouchDBUserProvider;
use App\Repository\LocationRepository;
use App\Repository\MariaDBNonBase64SessionHandler;
use App\Repository\PlaceRepository;
use Database\Seeders\ExhibitSeeder;
use Database\Seeders\ImageSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\PlaceSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Session;
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
		
		Session::extend('mariadb_json', static function(Application $app) {
			$connection_name = config('session.connection');

			$connection = $app->make('db')->connection($connection_name);
			
			$table = config('session.table');
			$lifetime = config('session.lifetime');
			
			return new MariaDBNonBase64SessionHandler($connection, $table, $lifetime, $app);
		});
		
		View::addNamespace('errors', resource_path('views/errors'));
		
		Vite::prefetch(concurrency: 3);
	}
}
