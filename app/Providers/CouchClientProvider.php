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
			return new CouchClient(config('couchdb.url'), config('couchdb.database'), [
				'username' => config('couchdb.username'),
				'password' => config('couchdb.password'),
			]);
		});
		$this->app->singleton(CouchClient::class.'.admin', static function (Application $app): CouchClient {
			return new CouchClient(env('COUCHDB_URL'), config('couchdb.database'), [
				'username' => config('couchdb.admin_username'),
				'password' => config('couchdb.admin_password'),
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
