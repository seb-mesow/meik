<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\Currency;

class Price
{
	/**
	 * Währungsbetrag in der kleinsten Einheit der Währung (z.B. Pfennig) (öffentlich)
	 */
	private int $amount;
	
	/**
	 * historische Währung (öffentlich)
	 * 
	 * @Accessor(getter="get_currency")
	 */
	private Currency $currency;

	/**
	 * Get the value of amount
	 */ 
	public function get_amount()
	{
		return $this->amount;
	}

	/**
	 * Set the value of amount
	 *
	 * @return  self
	 */ 
	public function set_amount($amount)
	{
		$this->amount = $amount;

		return $this;
	}

	/**
	 * Get the value of currency
	 */ 
	public function get_currency()
	{
		return $this->currency;
	}

	/**
	 * Set the value of currency
	 *
	 * @return  self
	 */ 
	public function set_currency(Currency $currency)
	{
		$this->currency = $currency;

		return $this;
	}
}
