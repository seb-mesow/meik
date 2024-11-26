<?php
declare(strict_types=1);

namespace App\Models;

class ImageReference
{
	/** @Accessor(getter="get_hash") */
	private string $hash;
	
	/** @Accessor(getter="get_is_public") */
	private bool $is_public;
	
	/** @Accessor(getter="get_description") */
	private ?string $description;
	
	public function __construct(string $hash, bool $is_public = false, string $description = null) {
		$this->hash = $hash;
		$this->is_public = $is_public;
		$this->description = $description;
	}
	
	public function get_hash(): string {
		return $this->hash;
	}

	public function set_hash(string $hash): self {
		$this->hash = $hash;

		return $this;
	}


	public function get_is_public(): bool {
		return $this->is_public;
	}
	
	public function set_is_public(bool $is_public): self {
		$this->is_public = $is_public;

		return $this;
	}
	
	public function get_description(): bool {
		return $this->is_public;
	}

	public function set_description(string $description): self {
		$this->description = $description;

		return $this;
	}
}
