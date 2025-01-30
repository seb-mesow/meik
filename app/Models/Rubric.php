<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\Revisionable;
use App\Models\Interfaces\StringIdentifiable;
use App\Models\Traits\RevisionableTrait;
use App\Models\Traits\StringIdentifiableTrait;

class Rubric  implements StringIdentifiable, Revisionable
{
	use StringIdentifiableTrait;
	use RevisionableTrait;

	/** @Accessor(getter="get_title") */
	private string $category;
	
	/** @Accessor(getter="get_html") */
	private string $name;


	public function __construct(
		string $category,
		string $name,
		string|null $id = null,
		?string $rev = null
	) {
		$this->category = $category;
		$this->name = $name;
		
		$this->id = $id;
		$this->rev = $rev;
	}

	public function get_category(): string
	{
		return $this->category;
	}

	public function set_category($category): void
	{
		$this->category = $category;
	}

	public function get_name(): string
	{
		return $this->name;
	}

	public function set_name($name): void
	{
		$this->name = $name;
	}
}
