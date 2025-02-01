<?php
declare(strict_types=1);

namespace App\Models;
use App\Models\Interfaces\Revisionable;
use App\Models\Interfaces\StringIdentifiable;
use App\Models\Traits\RevisionableTrait;
use App\Models\Traits\StringIdentifiableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use RuntimeException;
use SensitiveParameter;

class User implements Authenticatable, StringIdentifiable, Revisionable
{
	use StringIdentifiableTrait;
	use RevisionableTrait;
	
	/**
	 * allgemeine ID zur Identifizierung, muss auch eindeutig sein
	 */
	private string $username;
	private string $password_hash;
	private string $forename;
	private string $surname;
	private bool $is_admin = false;
	
	/**
	 * ist optional: nur wenn der User sich beim letzten Einloggen mit "Remember Me" angemeldet hat, ist er gesetzt 
	 */
	private ?string $remember_token = null;
	
	public function __construct(
		string $username,
		#[SensitiveParameter] string $password_hash,
		bool $is_admin,
		string $forename,
		string $surname,
		?string $original_username = null,
		?string $rev = null
	) {
		$this->username = $username;
		$this->password_hash = $password_hash;
		$this->forename = $forename;
		$this->surname = $surname;
		$this->is_admin = $is_admin;
		
		$this->id = $original_username;
		$this->rev = $rev;
	}
	
	/**
	 * returns the attribute name for the primary keys
	 * @return string
	 */
	public function getAuthIdentifierName(): string {
		return '_id';
	}
	
	/**
	 * returns the primary key
	 */
	public function getAuthIdentifier(): string {
		assert($this->id !== null);
		return $this->id;
	}
	
	/**
	 * returns the attribute name for the password hashes
	 */
	public function getAuthPasswordName(): string {
		return 'password_hash';
	}
	
	/**
	 * returns the password hash
	 */
	public function getAuthPassword(): string {
		return $this->get_password_hash();
	}
	
	/**
	 * @return string
	 */
	public function getRememberToken(): ?string {
		return $this->remember_token;
	}
	
	/**
	 * @param string $new_remember_token
	 * @return void
	 */
	public function setRememberToken($new_remember_token) {
		assert(is_string($new_remember_token));
		$this->remember_token = $new_remember_token;
	}
	
	/**
	 * @return string
	 */
	public function getRememberTokenName(): string {
		return 'remember_token';
	}
	
	public function get_username(): string {
		return $this->username;
	}
	
	public function set_username(string $username): void {
		$this->username = $username;
	}
	
	public function get_password_hash(): string {
		return $this->password_hash;
	}
	
	public function set_password_hash(string $password_hash): void {
		$this->password_hash = $password_hash;
	}
	
	public function get_forename(): string {
		return $this->forename;
	}
	
	public function set_forename(string $forename): void {
		$this->forename = $forename;
	}
	
	public function get_surname(): string {
		return $this->surname;
	}
	
	public function set_surname(string $surname): void {
		$this->surname = $surname;
	}
	
	public function is_admin(): bool {
		return $this->is_admin;
	}
	
	public function set_is_admin(bool $is_admin): void {
		$this->is_admin = $is_admin;
	}
}
