<?php
declare(strict_types=1);

namespace App\Models;

class FreeText
{
	/** @Accessor(getter="get_title") */
	private ?string $title = null;
	/** @Accessor(getter="get_html") */
	private ?string $html = null;
	/** @Accessor(getter="get_is_public") */
	private bool $is_public = false;

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
	 * Get the value of html
	 */
	public function get_html()
	{
		return $this->html;
	}

	/**
	 * Set the value of html
	 *
	 * @return  self
	 */
	public function set_html($html)
	{
		$this->html = $html;

		return $this;
	}

	/**
	 * Get the value of is_public
	 */
	public function get_is_public()
	{
		return $this->is_public;
	}

	/**
	 * Set the value of is_public
	 *
	 * @return  self
	 */
	public function set_is_public($is_public)
	{
		$this->is_public = $is_public;

		return $this;
	}
}
