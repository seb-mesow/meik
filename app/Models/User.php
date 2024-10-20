<?php
declare(strict_types=1);

namespace App\Models;
use App\Repository\CouchDBUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\App;
use Random\Randomizer;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

class User implements Authenticatable
{
	
	public function __construct(
		public readonly string $original_name,
		public readonly string $name,
		public readonly string $password,
		public readonly bool $is_admin = false,
		public string $remember_token = '',
		public readonly ?string $rev = null,
	) {}
		
	public function getAuthIdentifierName(): string {
		return 'original_name';
	}
	
    public function getAuthIdentifier(): string {
		return $this->original_name;
	}
	
    public function getAuthPasswordName(): string {
		return 'password';
	}
	
    public function getAuthPassword(): string {
		return $this->password;
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
}
