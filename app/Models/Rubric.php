<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\Category;
use App\Models\Interfaces\Revisionable;
use App\Models\Interfaces\StringIdentifiable;
use App\Models\Traits\RevisionableTrait;
use App\Models\Traits\StringIdentifiableTrait;

class Rubric  implements StringIdentifiable, Revisionable
{
	use StringIdentifiableTrait;
	use RevisionableTrait;

	/** @Accessor(getter="get_category") */
	private Category $category;
	
	/** @Accessor(getter="get_name") */
	private string $name;


	public function __construct(
		string $name,
		Category $category,
		?string $id = null,
		?string $rev = null
	) {
		$this->name = $name;
		$this->category = $category;
		
		$this->id = $id;
		$this->rev = $rev;
	}
	
	public function get_name(): string
	{
		return $this->name;
	}

	public function set_name(string $name): void
	{
		$this->name = $name;
	}
	
	public function get_category(): Category
	{
		return $this->category;
	}

	public function set_category(Category $category): void
	{
		$this->category = $category;
	}
}
