<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;

class Acquisation
{
	/** @Accessor(getter="get_kind") */
	private ?string $kind = null;
	/** @Accessor(getter="get_date") */
	private ?DateTime $date = null;
	/** @Accessor(getter="get_purchasing_price") */
	private bool $purchasing_price = false;

	/**
	 * Get the value of kind
	 */
	public function get_kind()
	{
		return $this->kind;
	}

	/**
	 * Set the value of kind
	 *
	 * @return  self
	 */
	public function set_kind($kind)
	{
		$this->kind = $kind;

		return $this;
	}

	/**
	 * Get the value of date
	 */
	public function get_date()
	{
		return $this->date;
	}

	/**
	 * Set the value of date
	 *
	 * @return  self
	 */
	public function set_date($date)
	{
		$this->date = $date;

		return $this;
	}

	/**
	 * Get the value of purchasing_price
	 */ 
	public function get_purchasing_price()
	{
		return $this->purchasing_price;
	}

	/**
	 * Set the value of purchasing_price
	 *
	 * @return  self
	 */ 
	public function set_purchasing_price($purchasing_price)
	{
		$this->purchasing_price = $purchasing_price;

		return $this;
	}
}
