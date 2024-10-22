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
		$this->create_user(new User('sebastian', 'sebastian', '_sEbAsTiAn=123', true));
		$this->create_user(new User('niklas', 'niklas', '_nIkLaS=123', true));
		$this->create_user(new User('pepe', 'pepe', '_pEpE=123', true));
		$this->create_user(new User('enrico', 'enrico', '_eNrIcO=123', true));
	}
	
	private function create_user(User $user) {
		$existing_user = $this->user_provider->retrieveById($user->getAuthIdentifier());
		if ($existing_user) {
			$user = new User(
				$user->original_name,
				$user->name, 
				$user->password, 
				$user->is_admin, 
				$user->remember_token, 
				$existing_user->rev
			);
		}
		$this->user_provider->insert($user);
	}
}
