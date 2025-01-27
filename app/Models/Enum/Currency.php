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
	 * Britisches Pfund
	 */
	case GBP = 'GBP';
	
	/**
	 * Deutsche Mark (1948–1999/2002)
	 */
	case DEM = 'DEM';
	
	/**
	 * Mark der DDR (1951–1990)
	 */
	case DDM = 'DDM';
	
	/**
	 * Sowjetischer Rubel (1923–1992)
	 */
	case SUR = 'SUR';
	
	/**
	 * österreichischer Schilling (1947–1999/2002)
	 */
	case ATS = 'ATS';
	
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
}
