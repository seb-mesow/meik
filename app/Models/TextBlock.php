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
	 * Get the value of content
	 */ 
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Set the value of content
	 *
	 * @return  self
	 */ 
	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * Get the value of is_private
	 */ 
	public function getIs_private()
	{
		return $this->is_private;
	}

	/**
	 * Set the value of is_private
	 *
	 * @return  self
	 */ 
	public function setIs_private($is_private)
	{
		$this->is_private = $is_private;

		return $this;
	}
}
