<?php
declare(strict_types=1);

namespace App\Repository;

use App;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use Random\Randomizer;
use \stdClass;

/**
 * @phpstan-type UserDoc object{
 *     _id: string,
 *     _rev?: string,
 *     username: string,
 *     password: string,
 * 	   forename: string,
 *     surname: string,
 *     is_admin: bool,
 *     remember_token: string
 * }
 */
final class CouchDBUserProvider implements UserProvider {
	public const string ID_PREFIX = 'user:';
	private const int REMEMBER_TOKEN_LENGTH = 64;
	
	private ?Randomizer $randomizer;
	
	public function __construct(
		private readonly CouchClient $client
	) {
		return;
	}
	
	public function insert(User $user) {
		assert(!$user->rev);
		$user_doc = $this->create_doc_from_user($user);
		$this->client->storeDoc($user_doc);
	}
	
	public function update(User $user) {
		assert($user->rev);
		$user_doc = $this->create_doc_from_user($user);
		$this->client->storeDoc($user_doc);
	}
	
	public function delete(User $user) {
		assert($user->rev);
		$user_doc = $this->create_doc_from_user($user);
		$this->client->deleteDoc($user_doc);
	}
	
	/**
	 * @return User[]
	 */
	public function get_all(): array {
		$res = $this->client->find([
			'_id' => [ '$beginsWith' => self::ID_PREFIX ],
		]);
		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): User {
			return $_this->create_user_from_doc($doc);
		}, $res->docs);
	}
	
	public function find_by_username(string $username): ?User {
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
	 * @param string $identifier original_name
	 * @return ?User
	 */
    public function retrieveByToken($identifier, $token): ?User {
		$res = $this->client
			->key($token)
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
		$res = $this->client
			->key([$credentials['username'], $credentials['password']])
			->include_docs(true)
			->getView('user', 'by-credentials');
		$rows = $res->rows;
		if ($rows) {
			$first = $rows[0]->doc;
			return $this->create_user_from_doc($first);
		}
		return null;
	}
	
    public function validateCredentials(Authenticatable $user, array $credentials): bool {
		return true;
	}
	
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): string {
		return $credentials['password'];
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
		$original_name = substr($doc->_id, strlen(self::ID_PREFIX));
		return new User(
			$original_name, 
			$doc->username,
			$doc->password,
			$doc->forename,
			$doc->surname,
			$doc->is_admin,
			$doc->remember_token,
			$doc->_rev
		);
	}
	
	/**
	 * @return UserDoc
	 */
	private function create_doc_from_user(User $user): stdClass {
		/** @var UserDoc */
		$user_doc = new stdClass();
		$user_doc->_id = self::ID_PREFIX . $user->original_username;
		if ($user->rev) {
			$user_doc->_rev = $user->rev;
		}
		$user_doc->username = $user->username;
		$user_doc->password = $user->password;
		$user_doc->forename = $user->forename;
		$user_doc->surname = $user->surname;
		$user_doc->is_admin = $user->is_admin;
		$user_doc->remember_token = $user->remember_token;
		return $user_doc;
	}
}
