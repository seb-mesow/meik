<?php
declare(strict_types=1);

namespace App\Models\Parts;

use App\Models\Enum\KindOfAcquistion;
use Illuminate\Support\Carbon;

class AcquisitionInfo
{
	/** 
	 * Zugangsdatum (intern)
	 * 
	 * @Accessor(getter="get_date") 
	 * @Type("DateTime")
	 */
	#[Expose]
	private Carbon $date;
	
	/**
	 * Angaben zur Herkunft (zunächst intern, könnte auch öffentlich sein)
	 * 
	 * (Von wem kommt das Exponat in das Museum?)
	 */
	private string $source;
	
	/**
	 * Art des Zugangs (intern)
	 * 
	 * Eine dieser Möglichkieten: Schenkung, Kauf, Fund, Überlassung
	 * 
	 * @Accessor(getter="get_kind")
	 */
	private KindOfAcquistion $kind;
	
	/**
	 * Kaufpreis in Cent (intern)
	 * 
	 * @Accessor(getter="get_purchasing_price")
	 */
	private ?int $purchasing_price;
	
	public function __construct(
		
	) {
		
	}
}
