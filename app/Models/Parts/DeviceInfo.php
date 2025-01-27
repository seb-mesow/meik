<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Support\Carbon;

class DeviceInfo
{
	/**
	 * partielles Startdatum des Vertriebs dieses Produktes (öffentlich)
	 * 
	 * gültige Formate: YYYY, YYYY-MM, YYYY-MM-DD
	 */
	private string $manufactured_from_date;
	
	/**
	 * partielles Entdatum des Vertriebs dieses Produktes
	 * 
	 * gültige Formate: YYYY, YYYY-MM, YYYY-MM-DD (öffentlich)
	 */
	private string $manufactured_to_date;
	
	
}
