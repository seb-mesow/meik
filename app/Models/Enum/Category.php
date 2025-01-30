<?php
declare(strict_types=1);

namespace App\Models\Enum;

enum Category: string
{
	case HARDWARE = 'hardware';
	case SOFTWARE = 'software';
	case BOOK = 'book';
	case OTHER = 'other';
	
	public function get_pretty_name(): string {
		return match($this) {
			self::HARDWARE => 'Hardware',
			self::SOFTWARE => 'Software',
			self::BOOK => 'Bücher',
			self::OTHER => 'Sonstiges',
		};
	}
}
