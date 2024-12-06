<?php
declare(strict_types=1);

namespace App\Models\Traits;

use RuntimeException;

trait IdentifiableTrait
{
	private readonly ?string $id;
	
	public function get_nullable_id(): ?string {
		if (is_null($this->id) || (is_string($this->id) && $this->id !== '')) {
			return $this->id;
		}
		throw new RuntimeException("Malformed nullable ID");
	}
	
	public function get_id(): string {
		if (is_string($this->id) && ($this->id !== '')) {
			return $this->id;
		};
		throw new RuntimeException("Malformed nullable ID");
	}
}
