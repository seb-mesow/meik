<?php
declare(strict_types=1);

namespace App\Models\Enum;

/**
 * Historische Währung nach ISO 4217
 */
enum Currency: string
{
	/**
	 * Euro, seit 1999/2002
	 */
	case EUR = 'EUR';
	
	/**
	 * US-Dollar
	 */
	case USD = 'USD';
	
	/**
	 * Britisches Pfund
	 */
	case GBP = 'GBP';
	
	/**
	 * Schweizer Franken
	 */
	case CHF = 'CHF';
	
	/**
	 * (neuer) Russischer Rubel, seit 1998
	 */
	case RUB = 'RUB';
	
	/**
	 * Tschechische Krone, seit 1993
	 */
	case CZK = 'CZK';
	
	/**
	 * (Neuer) Polnischer Zloty, seit 1994
	 */
	case PLN = 'PLN';
	
	/**
	 * Chinesischer Renminbi Yuan, seit 1949
	 */
	case CNY = 'CNY';
	
	/**
	 * Neuer Taiwan-Dollar
	 */
	case TWD = 'TWD';
	
	/**
	 * Japanischer Yen
	 */
	case JPY = 'JPY';
	
	/**
	 * Südkoreanischer Won
	 */
	case KRW = 'KRW';
	
	/**
	 * Deutsche Mark (1948–1999/2002)
	 */
	case DEM = 'DEM';
	
	/**
	 * Mark der DDR (1951–1990)
	 */
	case DDM = 'DDM';
	
	/**
	 * österreichischer Schilling (1947–1999/2002)
	 */
	case ATS = 'ATS';
	
	/**
	 * Französische Franc (1958–1999/2002)
	 */
	case FRF = 'FRF';
	
	/**
	 * Sowjetischer Rubel (1923–1992)
	 */
	case SUR = 'SUR';
	
	/**
	 * Tschechoslowakische Krone (1953–1993)
	 */
	case CSK = 'CSK';
	
	/**
	 * alter Polnischer Zloty (1950-1994)
	 */
	case PLZ = 'PLZ';
	
	/**
	 * (alter) russischer Rubel (1991-1998)
	 */
	case RUR = 'RUR';
	
	public function get_name(): string {
		return match($this) {
			self::EUR => 'Euro',
			self::USD => 'US-Dollar',
			self::GBP => 'Britisches Pfund',
			self::CHF => 'Schweizer Franken',
			self::RUB => 'Russischer Rubel',
			self::CZK => 'Tschechische Krone',
			self::PLN => '(Neuer) Polnischer Zloty',
			self::CNY => 'Chinesischer Renminbi Yuan',
			self::TWD => 'Neuer Taiwan-Dollar',
			self::JPY => 'Japanischer Yen',
			self::KRW => 'Südkoreanischer Won',
			self::DEM => 'Deutsche Mark',
			self::DDM => 'Mark der DDR',
			self::ATS => 'Schilling',
			self::FRF => 'Französischer Franc',
			self::SUR => 'Sowjetischer Rubel',
			self::CSK => 'Tschechoslowakische Krone',
			self::PLZ => 'alter Polnischer Zloty',
			self::RUR => '(alter) russischer Rubel',
		};
	}
}
