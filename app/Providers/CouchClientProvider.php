<?php
declare(strict_types=1);

namespace App\Providers;

use App\Util\StringIdGenerator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use PHPOnCouch\CouchAdmin;
use PHPOnCouch\CouchClient;

final class CouchClientProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		$this->app->singleton(CouchClient::class, static function (Application $app): CouchClient {
			return new CouchClient(env('COUCHDB_URL'), env('COUCHDB_DATABASE'), [
				'username' => env('COUCHDB_USERNAME'),
				'password' => env('COUCHDB_PASSWORD'),
			]);
		});
		$this->app->singleton(CouchClient::class.'.admin', static function (Application $app): CouchClient {
			return new CouchClient(env('COUCHDB_URL'), env('COUCHDB_DATABASE'), [
				'username' => env('COUCHDB_ADMIN_USERNAME'),
				'password' => env('COUCHDB_ADMIN_PASSWORD'),
			]);
		});
		$this->app->singleton(StringIdGenerator::class);
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		//
	}
}
