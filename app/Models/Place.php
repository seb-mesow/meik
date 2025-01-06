<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\Revisionable;
use App\Models\Interfaces\StringIdentifiable;
use App\Models\Traits\RevisionableTrait;
use App\Models\Traits\StringIdentifiableTrait;

class Place implements StringIdentifiable, Revisionable
{
	use StringIdentifiableTrait;
	use RevisionableTrait;
	
	/** @Accessor(getter="get_name") */
	private string $name;

	/** @Accessor(getter="get_location_id") */
	private string $location_id;
	
	public function __construct(
		string $name,
		string $location_id,
		?string $id = null,
		?string $rev = null
	) {
		$this->name = $name;
		$this->location_id = $location_id;
		$this->id = $id;
		$this->rev = $rev;
	}
	
	public function get_name(): string {
		return $this->name;
	}

	public function set_name(string $name): void {
		$this->name = $name;
	}

	public function get_location_id(): string {
		return $this->location_id;
	}

	public function set_location_id(string $location_id): void {
		$this->location_id = $location_id;
	}
}
