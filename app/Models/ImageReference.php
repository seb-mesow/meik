<?php

declare(strict_types=1);

namespace App\Models;

class ImageReference
{
	/** @Accessor(getter="get_hash") */
	private ?string $hash = null;
	/** @Accessor(getter="get_is_public") */
	private ?string $is_public = null;

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
	public function set_is_public($is_public)
	{
		$this->is_public = $is_public;

		return $this;
	}
}
