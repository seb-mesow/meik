<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Facades\Date;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

class TextBlock
{
	private string $title;
	private string $content;
	private string $is_private;

	/**
	 * Get the value of title
	 */ 
	public function get_title()
	{
		return $this->title;
	}

	/**
	 * Set the value of title
	 *
	 * @return  self
	 */ 
	public function set_title($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Get the value of content
	 */ 
	public function get_content()
	{
		return $this->content;
	}

	/**
	 * Set the value of content
	 *
	 * @return  self
	 */ 
	public function set_content($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * Get the value of is_private
	 */ 
	public function get_is_private()
	{
		return $this->is_private;
	}

	/**
	 * Set the value of is_private
	 *
	 * @return  self
	 */ 
	public function set_is_private($is_private)
	{
		$this->is_private = $is_private;

		return $this;
	}
}
