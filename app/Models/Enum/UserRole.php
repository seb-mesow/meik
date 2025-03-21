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
	
	private function get_rank_number(): int {
		return match($this) {
			self::READER => 0,
			self::EDITOR => 1,
			self::ADMIN => 99,
		};
	}
	
	public function is_at_least(UserRole $cmp_role): bool {
		return $this->get_rank_number() >= $cmp_role->get_rank_number();
	}
}
