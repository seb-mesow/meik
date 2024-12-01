<?php

declare(strict_types=1);

namespace App\Models;

class Place
{
	/** @Accessor(getter="get__id") */
	private ?string $_id = null;
	/** @Accessor(getter="get__rev") */
	private ?string $_rev = null;
	/** @Accessor(getter="get_name") */
	private ?string $name = null;
	/** @Accessor(getter="get_is_public") */
	private ?string $is_public = null;
	/** @Accessor(getter="get_location") */
	private ?string $location = null;

	/**
	 * Get the value of _id
	 */
	public function get__id()
	{
		return $this->_id;
	}

	/**
	 * Set the value of _id
	 *
	 * @return  self
	 */
	public function set__id($_id): self 
	{
		$this->_id = $_id;

		return $this;
	}

	/**
	 * Get the value of _rev
	 */
	public function get__rev()
	{
		return $this->_rev;
	}

	/**
	 * Set the value of _rev
	 *
	 * @return  self
	 */
	public function set__rev($_rev): self
	{
		$this->_rev = $_rev;

		return $this;
	}

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
	public function set_name($name): self
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get the value of is_public
	 */
	public function get_is_public()
	{
		return $this->is_public;
	}

	/**
	 * Set the value of is_public
	 *
	 * @return  self
	 */
	public function set_is_public($is_public): self
	{
		$this->is_public = $is_public;

		return $this;
	}

	public function getLocation(): ?string
	{
		return $this->location;
	}

	public function setLocation(?string $location): self
	{
		$this->location = $location;
		return $this;
	}
}
