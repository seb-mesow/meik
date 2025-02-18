<?php
declare(strict_types=1);

namespace App\Models\Parts;

use App\Models\Enum\Currency;

class Price
{
	/**
	 * Währungsbetrag in der kleinsten Einheit der Währung (z.B. Pfennig) (öffentlich)
	 * 
	 * Pflicht (wenn Originalpreis überhaupt angegeben)
	 */
	private int $amount;
	
	/**
	 * historische Währung (öffentlich)
	 * 
	 * Pflicht (wenn Originalpreis überhaupt angegeben)
	 * 
	 * @Accessor(getter="get_currency")
	 */
	private Currency $currency;
	
	public function __construct(
		int $amount,
		Currency $currency,
	) {
		$this->amount = $amount;
		$this->currency = $currency;
	}
	
	public function get_amount(): int
	{
		return $this->amount;
	}
	
	public function get_amount_in_main_unit(): float
	{
		return $this->amount / (float) 100;
	}

	public function set_amount($amount): void
	{
		$this->amount = $amount;
	}
	
	public function get_currency(): Currency
	{
		return $this->currency;
	}

	public function set_currency(Currency $currency): void
	{
		$this->currency = $currency;
	}
}
