<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Facades\Date;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

class Exhibit
{
	private ?string $title = null;
	private ?string $html = null;
	private boolean $is_public = false;

	/**
	 * Get the value of title
	 */ 
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set the value of title
	 *
	 * @return  self
	 */ 
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Get the value of html
	 */ 
	public function getHtml()
	{
		return $this->html;
	}

	/**
	 * Set the value of html
	 *
	 * @return  self
	 */ 
	public function setHtml($html)
	{
		$this->html = $html;

		return $this;
	}

	/**
	 * Get the value of is_public
	 */ 
	public function getIs_public()
	{
		return $this->is_public;
	}

	/**
	 * Set the value of is_public
	 *
	 * @return  self
	 */ 
	public function setIs_public($is_public)
	{
		$this->is_public = $is_public;

		return $this;
	}
}