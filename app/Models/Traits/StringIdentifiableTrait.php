<?php
declare(strict_types=1);

namespace App\Models\Traits;

use RuntimeException;

trait StringIdentifiableTrait
{
	private ?string $id;
	
	public function get_nullable_id(): ?string {
		if (($this->id === null) || (is_string($this->id) && ($this->id !== ''))) {
			return $this->id;
		}
		throw new RuntimeException("Malformed nullable, string ID");
	}
	
	public function get_id(): string {
		if (is_string($this->id) && ($this->id !== '')) {
			return $this->id;
		};
		throw new RuntimeException("Malformed string ID");
	}
	
	public function set_id(string $id): void {
		if ($id === '') {
			throw new RuntimeException("Malformed new string ID");
		}
		if ($this->id !== $id) {
			if (is_string($this->id)) {
				throw new RuntimeException("Cannot reassign string ID");
			}
			$this->id = $id;
		}
	}
}
