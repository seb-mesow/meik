<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\IntIdentifiable;
use App\Models\Interfaces\Revisionable;
use App\Models\Interfaces\StringIdentifiable;
use Illuminate\Support\Facades\Date;
use App\Models\Traits\IntIdentifiableTrait;
use App\Models\Traits\RevisionableTrait;
use App\Models\Traits\StringIdentifiableTrait;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

class Rubric  implements StringIdentifiable, Revisionable
{
	use StringIdentifiableTrait;
	use RevisionableTrait;

	/** @Accessor(getter="get_title") */
	private ?string $category = null;
	/** @Accessor(getter="get_html") */
	private ?string $name = null;


	public function __construct(
		string $category,
		string $name,
		string|null $id = null,
		?string $rev = null
	) {
		$this->id = $id;
		$this->rev = $rev;
		
		$this->category = $category;
		$this->name = $name;
	}


	/**
	 * Get the value of category
	 */
	public function get_category()
	{
		return $this->category;
	}

	/**
	 * Set the value of category
	 *
	 * @return  self
	 */
	public function set_category($category)
	{
		$this->category = $category;

		return $this;
	}

	/**
	 * Get the value of name
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * Set the value of name
	 *
	 * @return  self
	 */
	public function set_name($name)
	{
		$this->name = $name;

		return $this;
	}
}
