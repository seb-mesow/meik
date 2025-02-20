<?php
declare(strict_types=1);

namespace App\Models\Enum;

/**
 * Art des Zugangs eines Exponates
 */
enum KindOfProperty: string
{
	/**
	 * Eigentum
	 */
	case PROPERTY = 'property';
	
	/**
	 * Leihe
	 */
	case LOAN = 'loan';
	
	/**
	 * Miete
	 */
	case RENT = 'rent';
	
	public function get_id(): string {
		return $this->value;
	}
	
	public function get_name(): string {
		return match($this) {
			self::PROPERTY => 'Eigentum',
			self::LOAN => 'Leihe',
			self::RENT => 'Miete',
		};
	}
}
