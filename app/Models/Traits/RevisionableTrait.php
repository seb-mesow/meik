<?php
declare(strict_types=1);

namespace App\Models\Traits;

use RuntimeException;

trait RevisionableTrait
{
	private ?string $rev;
	
	public function get_nullable_rev(): ?string {
		if (is_null($this->rev) || (is_string($this->rev) && $this->rev !== '')) {
			return $this->rev;
		}
		throw new RuntimeException("Malformed nullable revision ID");
	}
	
	public function get_rev(): string {
		if (is_string($this->rev) && ($this->rev !== '')) {
			return $this->rev;
		};
		throw new RuntimeException("Malformed nullable revision ID");
	}
	
	public function set_rev(string $new_rev): void {
		if ($new_rev === '') {
			throw new RuntimeException("Malformed new revision ID");	
		}
		$this->rev = $new_rev;
	}
}
