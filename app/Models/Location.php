<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\Revisionable;
use App\Models\Interfaces\StringIdentifiable;
use App\Models\Traits\RevisionableTrait;
use App\Models\Traits\StringIdentifiableTrait;

class Location implements StringIdentifiable, Revisionable
{
	use StringIdentifiableTrait;
	use RevisionableTrait;
	
	/** @Accessor(getter="get_name") */
	private string $name;
	
	/** @Accessor(getter="get_is_public") */
	private bool $is_public;
	
	public function __construct(
		string $name,
		bool $is_public = false,
		?string $id = null,
		?string $rev = null
	) {
		$this->name = $name;
		$this->is_public = $is_public;
		$this->id = $id;
		$this->rev = $rev;
	}

	public function get_name(): string {
		return $this->name;
	}

	public function set_name(string $name): void {
		$this->name = $name;
	}

	public function get_is_public(): bool {
		return $this->is_public;
	}

	public function set_is_public(bool $is_public): void {
		$this->is_public = $is_public;
	}
}
