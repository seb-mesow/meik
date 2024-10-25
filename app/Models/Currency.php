<?php

declare(strict_types=1);

namespace App\Models;

class Currency
{
	/** @Accessor(getter="get_name") */
	private ?string $name = null;
	/** @Accessor(getter="get_code") */
	private ?string $code = null;
	/** @Accessor(getter="get_country") */
	private ?string $country = null;

	/**
	 * Get the value of name
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * Set the value of name
	 *
	 * @return  self
	 */
	public function set_name($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get the value of code
	 */
	public function get_code()
	{
		return $this->code;
	}

	/**
	 * Set the value of code
	 *
	 * @return  self
	 */
	public function set_code($code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * Get the value of country
	 */ 
	public function get_country()
	{
		return $this->country;
	}

	/**
	 * Set the value of country
	 *
	 * @return  self
	 */ 
	public function set_country($country)
	{
		$this->country = $country;

		return $this;
	}
}
