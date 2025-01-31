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
	private string $remember_token = '';
	
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
	
	public function getAuthIdentifierName(): string {
		return 'original_name';
	}
	
	public function getAuthIdentifier(): string {
		return $this->username;
	}
	
	public function getAuthPasswordName(): string {
		throw new RuntimeException('not implemented by intention');
		// return 'password';
	}
	
	public function getAuthPassword(): string {
		throw new RuntimeException('not implemented by intention');
	}
	
	/**
	 * @return string
	 */
	public function getRememberToken(): string {
		return $this->remember_token;
	}
	
	/**
	 * @param mixed $new_remember_token
	 * @return void
	 */
	public function setRememberToken($new_remember_token) {
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
		return $this->forename;
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
