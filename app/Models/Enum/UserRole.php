<?php
declare(strict_types=1);

namespace App\Models\Enum;

enum UserRole: string
{
	case READER = 'reader';
	case EDITOR = 'editor';
	case ADMIN = 'admin';
	
	public function get_id(): string {
		return $this->value;
	}
	
	public function get_name(): string {
		return match($this) {
			self::READER => 'Leser',
			self::EDITOR => 'Editor',
			self::ADMIN => 'Admin',
		};
	}
}
