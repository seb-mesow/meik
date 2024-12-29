<?php
declare(strict_types=1);

namespace App\Models\Traits;

use RuntimeException;

trait IntIdentifiableTrait
{
	private readonly ?int $id;
	
	public function get_nullable_id(): ?int {
		if (is_null($this->id) || (is_int($this->id) && ($this->id >= 0))) {
			return $this->id;
		}
		throw new RuntimeException("Malformed nullable, integer ID");
	}
	
	public function get_id(): int {
		if (is_int($this->id) && ($this->id >= 0)) {
			return $this->id;
		};
		throw new RuntimeException("Malformed integer ID");
	}
}
