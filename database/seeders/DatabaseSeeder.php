<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Repository\CouchDBUserProvider;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(CouchDBUserProvider $provider): void
	{
		$this->call([
			SetupCouchDBSeeder::class,
			UserSeeder::class,
			ExhibitSeeder::class,
			LocationSeeder::class
		]);
	}
}
