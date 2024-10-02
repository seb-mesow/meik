<?php
declare(strict_types=1);

namespace App\Models;
use Illuminate\Contracts\Auth\Authenticatable;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

class User implements Authenticatable
{
	public function __construct(
		private readonly string $original_name,
		private readonly string $name,
		private readonly string $password,
		private readonly bool $is_admin,
	) {}
	
	public function getAuthIdentifierName(): string {
		return '_id';
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
	 * @deprecated TODO implementieren
	 *
	 * @return string
	 */
    public function getRememberToken(): string {
		return 'TODO';
	}
	
	/**
	 * @deprecated TODO implementieren
	 * 
	 * @param mixed $value
	 * @return void
	 */
    public function setRememberToken($value) {
		// TODO implementieren
	}
	
	/**
	 * @deprecated TODO implementieren
	 * 
	 * @return string
	 */
    public function getRememberTokenName(): string {
		return 'TODO';
	}
}
