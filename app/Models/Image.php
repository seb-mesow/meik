<?php

declare(strict_types=1);

namespace App\Models;

class Image
{
	/** @Accessor(getter="get_hash") */
	private ?string $hash = null;
	/** @Accessor(getter="get_data") */
	private mixed $data = null;

	/**
	 * Get the value of hash
	 */
	public function get_hash()
	{
		return $this->hash;
	}

	/**
	 * Set the value of hash
	 *
	 * @return  self
	 */
	public function set_hash($hash)
	{
		$this->hash = $hash;

		return $this;
	}

	/**
	 * Get the value of data
	 */
	public function get_data()
	{
		return $this->data;
	}

	/**
	 * Set the value of data
	 *
	 * @return  self
	 */
	public function set_data($data)
	{
		$this->data = $data;

		return $this;
	}
}
