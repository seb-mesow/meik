<?php

declare(strict_types=1);

namespace App\Models;

class Price
{
	/** @Accessor(getter="get_amount") */
	private ?float $amount = null;
	/** @Accessor(getter="get_currency") */
	private ?Currency $currency = null;

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
