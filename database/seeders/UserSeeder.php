<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Repository\CouchDBUserProvider;
use Database\Seeders\Traits\SeederTrait;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Seeder;
use PHPOnCouch\CouchClient;

class UserSeeder extends Seeder
{
	use SeederTrait;
	
	public function __construct(
		CouchClient $client,
		private readonly CouchDBUserProvider $user_provider,
		private readonly Hasher $hasher,
	) {
		$this->client = $client;
	}
	
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		$this->remove_all_documents_by_model_type_id(CouchDBUserProvider::MODEL_TYPE_ID);
		
		$this->create_user(
			username: 'sebastian',
			password: '_sEbAsTiAn=123',
			is_admin: true,
			forename: "Sebastian",
			surname: "Müller",
		);
		$this->create_user(
			username: 'niklas',
			password: '_nIkLaS=123',
			is_admin: true,
			forename: "Niklas",
			surname: "Haustein",
		);
		$this->create_user(
			username: 'pepe',
			password: '_pEpE=123',
			is_admin: true,
			forename: "Pepe",
			surname: "Sievert",
		);
		$this->create_user(
			username: 'enrico',
			password: '_eNrIcO=123',
			is_admin: true,
			forename: "Enrico",
			surname: "Schmidt",
		);
		$this->create_user(
			username: 'm'.'u'.'e'.'l'.'l'.'e'.'r',
			password: '_'.'m'.'U'.'e'.'L'.'l'.'E'.'r'.'='.'1'.'2'.'3',
			is_admin: true,
			forename: "U"."w"."e"."-"."J"."e"."n"."s",
			surname: "M"."ü"."l"."l"."e"."r",
		);
	}
	
	private function create_user(string $username,	string $password, bool $is_admin, string $forename,	string $surname): void {
		$user = new User(
			username: $username,
			password_hash: $this->hasher->make($password),
			is_admin: $is_admin,
			forename: $forename,
			surname: $surname,
		);
		$this->user_provider->insert($user);
	}
}
