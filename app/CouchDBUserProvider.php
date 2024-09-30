<?php
declare(strict_types=1);

namespace App;

use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use PHPOnCouch\CouchClient;

final class CouchDBUserProvider implements UserProvider {
	private const string ID_PREFIX = UserRepository::ID_PREFIX;
	
	public function __construct(
		private readonly CouchClient $client
	) {}
	
	/**
	 * @param string $identifier original_name
	 * @return User
	 */
	public function retrieveById($identifier): User {
		$this->client->getDoc(self::ID_PREFIX . $identifier);
	}
	
    public function retrieveByToken($identifier, $token): User {
		
	}
	
    public function updateRememberToken(Authenticatable $user, $token) {
		
	}
	
	/**
	 * @param array{username: string, password: string} $credentials
	 * @return User
	 */
	public function retrieveByCredentials(array $credentials): User {
		// TODO with views
	}
	
    public function validateCredentials(Authenticatable $user, array $credentials): bool {
		return true;
	}
	
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): string {
		return $credentials['password'];
	}
}
