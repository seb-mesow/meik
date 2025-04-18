<?php
declare(strict_types=1);

namespace App\Repository;

use App;
use App\Models\Enum\UserRole;
use App\Models\User;
use App\Repository\Traits\StringIdRepositoryTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use Random\Randomizer;
use Illuminate\Contracts\Hashing\Hasher;
use stdClass;

/**
 * @phpstan-type UserDoc object{
 *     _id: string,
 *     _rev?: string,
 *     username: string,
 *     password_hash: string,
 *     forename: string,
 *     surname: string,
 *     role: string,
 *     remember_token: string
 * }
 */
final class CouchDBUserProvider implements UserProvider {
	use StringIdRepositoryTrait;
	
	public const MODEL_TYPE_ID = 'user';
	private const int REMEMBER_TOKEN_LENGTH = 64;
	
	private ?Randomizer $randomizer;
	
	public function __construct(
		private readonly CouchClient $client,
		private readonly Hasher $hasher,
	) {}
	
	public function insert(User $user) {
		assert(!$user->get_nullable_id());
		assert(!$user->get_rev());
		$user_doc = $this->create_doc_from_user($user);
		$this->client->storeDoc($user_doc);
	}
	
	public function update(User $user) {
		assert($user->get_nullable_id());
		assert($user->get_rev());
		$user_doc = $this->create_doc_from_user($user);
		$this->client->storeDoc($user_doc);
	}
	
	public function remove(User $user) {
		assert($user->get_id());
		assert($user->get_rev());
		$user_doc = $this->create_doc_from_user($user);
		$this->client->deleteDoc($user_doc);
	}
	
	public function remove_by_id(string $user_id): void {
		$doc_id = $this->determinate_doc_id_from_model_id($user_id);
		$doc = $this->client->getDoc($doc_id); // retrieves _rev
		$this->client->deleteDoc($doc);
	}
	
	public function get(string $id): User {
		// Das Caching bei Places und Locations erspart ca. 750 ms .
		$_this = $this;
		return $this->cached(__FUNCTION__, $id, static function(string $_id) use ($_this): User {
			$doc_id = $_this->determinate_doc_id_from_model_id($_id);
			$user_doc = $_this->client->getDoc($doc_id);
			return $_this->create_user_from_doc($user_doc);
		}, $id);
	}
	
	/**
	 * @return User[]
	 */
	public function get_all(): array {
		$res = $this->client->find([
			'_id' => [
				'$beginsWith' => self::ID_PREFIX
			],
		]);
		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): User {
			return $_this->create_user_from_doc($doc);
		}, $res->docs);
	}
	
	/**
	 * @return array{
	 *     users: User[],
	 *     total_count: int
	 * }
	 */
	public function query(?int $page_number = null, ?int $count_per_page = null): array {
		$client = $this->client;
		if ($page_number !== null && $count_per_page !== null) {
			$client = $client
				->limit($count_per_page)
				->skip($page_number * $count_per_page);
		}
		
		$response = $client
			->reduce(false)
			->include_docs(true)
			->getView(self::MODEL_TYPE_ID, 'all');
		
		$users = $this->create_users_from_view_response($response);
		
		return [
			'users' => $users,
			'total_count' => $response->total_rows, // nur eine Request nötig, da zur Zeit keine Suchkriterien angebar
		];
	}
	
	public function find_by_username(string $username): ?User {
		$res = $this->client
			->key($username)
			->include_docs(true)
			->getView(self::MODEL_TYPE_ID, 'by-username');
		$rows = $res->rows;
		if ($rows) {
			$first = $rows[0]->doc;
			return $this->create_user_from_doc($first);
		}
		return null;
	}
	
	/**
	 * @param string $identifier username
	 * @return User|null
	 */
	public function retrieveById($identifier): ?User {
		try {
			$user_doc = $this->client->getDoc(self::ID_PREFIX . $identifier);
			return $this->create_user_from_doc($user_doc);
		} catch (CouchNotFoundException $e) {
			return null;
		}
	}
	
	/**
	 * @param string $user_id User-ID
	 * @param string $remember_token
	 * @return ?User
	 */
    public function retrieveByToken($user_id, $remember_token): ?User {
		$res = $this->client
			->key($remember_token)
			->include_docs(true)
			->getView('user', 'by-remember_token');
		$rows = $res->rows;
		if ($rows) {
			$first = $rows[0]->doc;
			return $this->create_user_from_doc($first);
		}
		return null;
	}
	
	public function updateRememberToken(Authenticatable $user, $token) {
		assert($user->getRememberToken() === $token);
		$this->insert($user);
	}
	
	/**
	 * @param array{username: string, password: string} $credentials
	 * @return ?User
	 */
	public function retrieveByCredentials(array $credentials): ?User {
		$username = $credentials['username'];
		
		$res = $this->client
			->key($username)
			->include_docs(true)
			->getView('user', 'by-username');
		$rows = $res->rows;
		if ($rows) {
			$first = $rows[0]->doc;
			return $this->create_user_from_doc($first);
		}
		return null;
	}
	
	public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials): bool {
		assert($user instanceof User);
		return $this->hasher->check($credentials['password'], $user->get_password_hash());
	}
	
	/**
	 * @param array{username: string, password: string} $credentials
	 */
	public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void {
		assert($user instanceof User);
		if ($force || $this->hasher->needsRehash($user->get_password_hash())) {
			$password = $credentials['password'];
			$user->set_password_hash($this->hasher->make($password));
			$this->update($user);
		}
	}
	
	/**
	 * @deprecated Laravel erzeugt selbst die Remember-Tokens
	 * @return string
	 */
	private function generate_remember_token(): string
	{
		if (!$this->randomizer) {
			$this->randomizer = App::make(Randomizer::class);
		}
		return base64_encode($this->randomizer->getBytes(self::REMEMBER_TOKEN_LENGTH));
	}
	
	/**
	 * @param UserDoc $doc
	 */
	private function create_user_from_doc(object $doc): User
	{
		return new User(
			username: $doc->username,
			password_hash: $doc->password_hash,
			forename: $doc->forename,
			surname: $doc->surname,
			role: UserRole::from($doc->role),
			id: $this->determinate_model_id_from_doc($doc),
			rev: $doc->_rev,
		);
	}
	
	/**
	 * @return UserDoc
	 */
	private function create_doc_from_user(User $user): stdClass {
		/** @var UserDoc */
		$user_doc = $this->create_stub_doc_from_model($user);
		
		$user_doc->username = $user->get_username();
		$user_doc->password_hash = $user->get_password_hash();
		$user_doc->forename = $user->get_forename();
		$user_doc->surname = $user->get_surname();
		$user_doc->role = $user->get_role()->get_id();
		$user_doc->remember_token = $user->getRememberToken();
		
		return $user_doc;
	}
	
	/**
	 * setzt als Nebeneffekt bei neuen Models die ID
	 * 
	 * @param User $user
	 * @return UserDoc
	 */
	private function create_stub_doc_from_model(User $user): stdClass {
		$stub_main_model_doc = new stdClass();
		if (is_null($user->get_nullable_id())) {
			// Die ID ist immer der erste Username, den der User je hatte.
			$user->set_id($user->get_username()); // <<<<<
		}
		$stub_main_model_doc->_id = $this->determinate_doc_id_from_model($user);
		if ($rev = $user->get_nullable_rev()) {
			$stub_main_model_doc->_rev = $rev;
		}
		return $stub_main_model_doc;
	}
	
	/**
	 * @return User[]
	 */
	private function create_users_from_view_response(stdClass $response): array {
		$_this = $this;
		return array_map(static function(stdClass $row) use ($_this): User {
			return $_this->create_user_from_doc($row->doc);
		}, $response->rows);
	}
}
