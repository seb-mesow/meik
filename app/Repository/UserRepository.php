<?php
declare(strict_types=1);

namespace App\Repository;
use PHPOnCouch\CouchClient;
use App\Models\User;

final class UserRepository {
	public const string ID_PREFIX = "user:";
	
	public function __construct(
		private readonly CouchClient $client
	) {}
	
	/**
	 * @param array{username: string, password: string, is_admin: bool} $data
	 * @return User
	 */
	public function create(string $username, string $password, bool $is_admin = false): User {
		// Hash::make($password)
		$this->client->storeDoc((object) [
			'_id' => self::ID_PREFIX . $username,
			'username' => $username,
			'password' => $password,
			'is_admin' => $is_admin,
		]);
		return new User($username, $username, $password, $is_admin); 
	}
}
