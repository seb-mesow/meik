<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Enum\UserRole;
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
			role: UserRole::ADMIN,
			forename: "Sebastian",
			surname: "Müller",
		);
		$this->create_user(
			username: 'niklas',
			password: '_nIkLaS=123',
			role: UserRole::ADMIN,
			forename: "Niklas",
			surname: "Haustein",
		);
		$this->create_user(
			username: 'pepe',
			password: '_pEpE=123',
			role: UserRole::ADMIN,
			forename: "Pepe",
			surname: "Sievert",
		);
		$this->create_user(
			username: 'enrico',
			password: '_eNrIcO=123',
			role: UserRole::ADMIN,
			forename: "Enrico",
			surname: "Schmidt",
		);
		$this->create_user(
			username: 'gruppe1',
			password: '_gRuPpe1=234',
			role: UserRole::EDITOR,
			forename: "Gruppe 1",
			surname: "MEIK",
		);
		$this->create_user(
			username: 'm'.'u'.'e'.'l'.'l'.'e'.'r',
			password: '_'.'m'.'U'.'e'.'L'.'l'.'E'.'r'.'='.'1'.'2'.'3',
			role: UserRole::ADMIN,
			forename: "U"."w"."e"."-"."J"."e"."n"."s",
			surname: "M"."ü"."l"."l"."e"."r",
		);
		
		for ($i = 0; $i < 100; $i++) {
			$this->create_user();
		}
	}
	
	private function create_user(
		?string $username = null,
		?string $password = null,
		?UserRole $role = null,
		?string $forename = null,
		?string $surname = null,
	): void {
		$username ??= fake()->userName();
		$user = new User(
			username: $username,
			password: $password ?? $username,
			role: $role ?? fake()->randomElement(UserRole::cases()),
			forename: $forename ?? fake()->firstName(),
			surname: $surname ?? fake()->lastName(),
		);
		$this->user_provider->insert($user);
	}
}
