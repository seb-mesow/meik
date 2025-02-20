<?php
declare(strict_types=1);

namespace App\Models\Enum;

/**
 * Sprache von Büchern nach ISO 639 Set 3
 */
enum Language: string
{
	case GERMAN = 'deu';
	case ENGLISH = 'eng';
	case CZECH = 'ces';
	case POLISH = 'pol';
	case FRENCH = 'fra';
	case DUTCH = 'nld';
	case RUSSIAN = 'rus';
	case JAPANESE = 'jpn';
	case KOREAN = 'kor';
	/**
	 * alle Arten von Chinesisch
	 */
	case CHINESE = 'zho';
	case SAXON = 'sn';
	
	public function get_id(): string {
		return $this->value;
	}
	
	public function get_name(): string {
		return match($this) {
			self::GERMAN => 'Deutsch',
			self::ENGLISH => 'Englisch',
			self::CZECH => 'Tschechisch',
			self::POLISH => 'Polnisch',
			self::FRENCH => 'Französisch',
			self::DUTCH => 'Niederländisch',
			self::RUSSIAN => 'Russisch',
			self::JAPANESE => 'Japanisch',
			self::KOREAN => 'Koreanisch',
			self::CHINESE => 'Chinesisch',
			self::SAXON => 'Sächsisch',
		};
	}
}
