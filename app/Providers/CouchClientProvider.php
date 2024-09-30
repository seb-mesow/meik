<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use PHPOnCouch\CouchClient;

final class CouchClientProvider extends ServiceProvider
{
	private const string COUCHDB_URL_DEFAULT = 'http://couchdb:5984';
	private const string COUCHDB_DATABASE_DEFAULT = 'meik';
	
	/**
	 * Register services.
	 */
	public function register(): void
	{
		$this->app->singleton(CouchClient::class, static function (Application $app) {
			return new CouchClient(env('COUCHDB_URL', self::COUCHDB_URL_DEFAULT), env('COUCHDB_DATABASE', self::COUCHDB_DATABASE_DEFAULT), [
				'username' => env('COUCHDB_USERNAME'),
				'password' => env('COUCHDB_PASSWORD'),
			]);
		});
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		//
	}
}
