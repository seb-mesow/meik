<?php
declare(strict_types=1);

namespace App\Models\Enum;

/**
 * Art des Zugangs eines Exponates
 */
enum KindOfAcquistion: string
{
	/**
	 * Kauf
	 */
	case PURCHASE = 'purchase';
	
	/**
	 * Schenkung
	 */
	case GIFT = 'gift';
	
	/**
	 * Fund
	 */
	case FIND = 'find';
	 
	 /**
	 * bei Leihgabe oder Miete: Überlassung
	 */
	case LOAN_OR_RENT = 'loan_or_rent';
	
}
