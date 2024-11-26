<?php

declare(strict_types=1);

namespace App\Models;

class Image
{
	/** @Accessor(getter="get_hash") */
	private ?string $hash = null;
	
	/** @Accessor(getter="get_data") */
	private string $data;

	public readonly ?string $rev = null;
	
	public function __construct(string $data, ?string $rev = null) {
		$this->data = $data;
		$this->rev = $rev;
	}
	
	/**
	 * Get the value of hash
	 */
	public function get_hash(): string
	{
		if (!$this->hash) {
			$this->hash = md5($this->data);
		}
		return $this->hash;
	}
	
	public function get_rev(): ?string {
		return $this->rev;
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
		$this->hash = null;
		
		return $this;
	}
}
