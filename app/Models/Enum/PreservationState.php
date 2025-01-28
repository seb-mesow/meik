<?php
declare(strict_types=1);

namespace App\Models\Enum;

/**
 * Erhaltungszustand eines Exponates
 */
enum PreservationState: string
{
	/**
	 * voll funktionsfähig
	 */
	case FULLY_FUNCTIONAL = 'fully_functional';
	
	/**
	 * funktionsfähig mit Einschränkungen
	 */
	case PARTIALLY_FUNCTIONAL = 'partially_functional';
	
	/**
	 * reparaturbedürftig
	 */
	case NEEDS_REPAIR = 'needs_repair';
	
	/**
	 * ohne Funktion, nur zum Ausstellen
	 */
	case DISPLAY_ONLY = 'display_only';
}
