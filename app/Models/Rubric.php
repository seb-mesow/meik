<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Facades\Date;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

class Rubric
{
    /** @Accessor(getter="get_title") */
	private ?string $category = null;
	/** @Accessor(getter="get_html") */
	private ?string $name = null;

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