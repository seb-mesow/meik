<?php
declare(strict_types=1);

namespace App\Models\Parts;

use App\Models\Enum\KindOfAcquisition;
use Illuminate\Support\Carbon;
use JMS\Serializer\Annotation\Expose;

class AcquisitionInfo
{
	/** 
	 * Zugangsdatum (intern)
	 * 
	 * Pflicht
	 * 
	 * @Accessor(getter="get_date") 
	 */
	#[Expose()]
	private Carbon $date;
	
	/**
	 * Angaben zur Herkunft (zunächst intern, könnte auch öffentlich sein)
	 * 
	 * (Von wem kommt das Exponat in das Museum?)
	 * 
	 * Pflicht
	 */
	private string $source;
	
	/**
	 * Art des Zugangs (intern)
	 * 
	 * Eine dieser Möglichkieten: Schenkung, Kauf, Fund, Überlassung
	 * 
	 * Pflicht
	 * 
	 * @Accessor(getter="get_kind")
	 */
	private ?KindOfAcquisition $kind;
	
	/**
	 * Kaufpreis in Cent (intern)
	 * 
	 * optional
	 * 
	 * @Accessor(getter="get_purchasing_price")
	 */
	private ?int $purchasing_price;
	
	public function __construct(
		Carbon $date,
		string $source,
		KindOfAcquisition|null $kind,
		int|null $purchasing_price,
	) {
		$this->date = $date;
		$this->source = $source;
		$this->kind = $kind;
		$this->purchasing_price = $purchasing_price;
	}
	
	public function get_date(): Carbon {
		return $this->date;
	}
	
	public function set_date(Carbon $date): void {
		$this->date = $date;
	}
	
	public function get_source(): string {
		return $this->source;
	}
	
	public function set_source(string $source): void {
		$this->source = $source;
	}
	
	public function get_kind(): KindOfAcquisition|null {
		return $this->kind;
	}
	
	public function set_kind(KindOfAcquisition|null $kind): void {
		$this->kind = $kind;
	}
	
	public function get_purchasing_price(): int|null {
		return $this->purchasing_price;
	}
	
	public function set_purchasing_price(int|null $price): void {
		$this->purchasing_price = $price;
	}
}
