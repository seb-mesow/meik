<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\Revisionable;
use App\Models\Interfaces\StringIdentifiable;
use App\Models\Traits\RevisionableTrait;
use App\Models\Traits\StringIdentifiableTrait;

class Image implements StringIdentifiable, Revisionable
{
	use StringIdentifiableTrait;
	use RevisionableTrait;
	
	private string $description;
	private bool $is_public;
	
	public function __construct(
		string $description = '',
		bool $is_public = false,
		?string $id = null,
		?string $rev = null
	) {
		$this->description = $description;
		$this->is_public = $is_public;
		$this->id = $id;
		$this->rev = $rev;
	}
	
	public function get_description(): string {
		return $this->description;
	}
	
	public function set_description(string $description): void {
		$this->description = $description;
	}
	
	public function get_is_public(): bool {
		return $this->is_public;
	}
	
	public function set_is_public(bool $is_public): void {
		$this->is_public = $is_public;
	}
}
