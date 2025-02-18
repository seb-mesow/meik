<?php
declare(strict_types=1);

namespace App\Models\Parts;

use App;
use App\Exceptions\InvalidPartialDateString;
use App\Util\PartialDateValidator;

class DeviceInfo
{
	/**
	 * partielles Startdatum des Vertriebs dieses Produktes (öffentlich)
	 * 
	 * gültige Formate: YYYY, YYYY-MM, YYYY-MM-DD
	 * 
	 * optional
	 */
	private ?string $manufactured_from_date;
	
	/**
	 * partielles Enddatum des Vertriebs dieses Produktes
	 * 
	 * gültige Formate: YYYY, YYYY-MM, YYYY-MM-DD (öffentlich)
	 * 
	 * optional
	 */
	private ?string $manufactured_to_date;
	
	public function __construct(
		string $manufactured_from_date,
		string $manufactured_to_date,
	) {
		$this->set_manufactured_from_date($manufactured_from_date);
		$this->set_manufactured_to_date($manufactured_to_date);
	}
	
	public function get_manufactured_from_date(): string {
		return $this->manufactured_from_date;
	}
	
	/**
	 * @throws InvalidPartialDateString
	 */
	public function set_manufactured_from_date(string $manufactured_from_date): void {
		$this->validate_partial_date_string($manufactured_from_date);
		$this->manufactured_from_date = $manufactured_from_date;
	}
	
	public function get_manufactured_to_date(): string {
		return $this->manufactured_to_date;
	}
	
	/**
	 * @throws InvalidPartialDateString
	 */
	public function set_manufactured_to_date(string $manufactured_to_date): void {
		$this->validate_partial_date_string($manufactured_to_date);
		$this->manufactured_to_date = $manufactured_to_date;
	}
	
	/**
	 * @throws InvalidPartialDateString
	 */
	private function validate_partial_date_string(string $partial_date_string): void {
		App::make(PartialDateValidator::class)->validate_string($partial_date_string);
	}
}
