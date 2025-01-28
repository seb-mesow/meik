<?php
declare(strict_types=1);

namespace App\Models\Parts;

use App\Exceptions\InvalidPartialDateString;

class DeviceInfo
{
	/**
	 * partielles Startdatum des Vertriebs dieses Produktes (öffentlich)
	 * 
	 * gültige Formate: YYYY, YYYY-MM, YYYY-MM-DD
	 */
	private string $manufactured_from_date;
	
	/**
	 * partielles Enddatum des Vertriebs dieses Produktes
	 * 
	 * gültige Formate: YYYY, YYYY-MM, YYYY-MM-DD (öffentlich)
	 */
	private string $manufactured_to_date;
	
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
		if (preg_match('/^\d\d\d\d-\d\d-\d\d$/', $partial_date_string) === 1) {
			return;
		}
		if (preg_match('/^\d\d\d\d-\d\d$/', $partial_date_string) === 1) {
			return;
		}
		if (preg_match('/^\d\d\d\d$/', $partial_date_string) === 1) {
			return;
		}
		throw new InvalidPartialDateString($partial_date_string);
	}
}
