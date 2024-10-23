<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Repository\CouchDBUserProvider;
use Illuminate\Database\Seeder;
use PHPOnCouch\CouchClient;

class UserSeeder extends Seeder
{
	public function __construct(
		private readonly CouchDBUserProvider $user_provider
	) {}
	
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
		$this->create_user(new User(
			'sebastian', 'sebastian', '_sEbAsTiAn=123',
			"Sebastian", "Müller",
			true));
		$this->create_user(new User(
			'niklas', 'niklas', '_nIkLaS=123',
			"Niklas", "Haustein",
			true));
		$this->create_user(new User(
			'pepe', 'pepe', '_pEpE=123', 
			"Pepe", "Sievert",
			true));
		$this->create_user(new User(
			'enrico', 'enrico', '_eNrIcO=123',
			"Enrico", "Schmidt",
			true));
	}
	
	private function create_user(User $user) {
		$existing_user = $this->user_provider->retrieveById($user->getAuthIdentifier());
		if ($existing_user) {
			$user = new User(
				$user->original_username,
				$user->username, 
				$user->password,
				$user->forename,
				$user->surname,
				$user->is_admin, 
				$user->remember_token, 
				$existing_user->rev
			);
		}
		$this->user_provider->insert($user);
	}
}
