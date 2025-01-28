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
	case POLNISCH = 'pol';
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
}
