<?php
declare(strict_types=1);

namespace App\Repository;

use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use PHPOnCouch\CouchClient;


/**
 * @phpstan-type UserDoc object{
 *     _id: string,
 *     _rev: string,
 *     name: string,
 *     password: string,
 *     is_admin: bool
 * }
 */
final class CouchDBUserProvider implements UserProvider {
	private const string ID_PREFIX = 'user:';
	
	public function __construct(
		private readonly CouchClient $client
	) {
		return;
	}
	
	/**
	 * @param string $identifier original_name
	 * @return User
	 */
	public function retrieveById($identifier): User {
		$user_doc = $this->client->getDoc(self::ID_PREFIX . $identifier);
		return $this->create_user_from_doc($user_doc);
	}
	
    public function retrieveByToken($identifier, $token): User {
		return new User("sebastian", "sebastian", "sebastian", false);
	}
	
    public function updateRememberToken(Authenticatable $user, $token) {
		return;
	}
	
	/**
	 * @param array{username: string, password: string} $credentials
	 * @return ?User
	 */
	public function retrieveByCredentials(array $credentials): ?User {
		$all_docs = $this->client->include_docs(true)->getAllDocs();
		foreach($all_docs->rows as $row) {
			$doc = $row->doc;
			if (str_starts_with($doc->_id, self::ID_PREFIX)
			&& $doc->name == $credentials['username']
			&& $doc->password == $credentials['password']
			) {
				return $this->create_user_from_doc($doc);
			}
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
	 * @param UserDoc $doc
	 */
	private function create_user_from_doc(object $doc): User
	{
		$original_name = substr($doc->_id, strlen(self::ID_PREFIX));
		return new User($original_name, $doc->name, $doc->password, $doc->is_admin);
	}
}
