<?php
declare(strict_types=1);

namespace App\Models\Parts;

use App\Models\Enum\Language;

class BookInfo
{
	/**
	 * Autoren (öffentlich)
	 * 
	 * @Accessor(getter="get_author") 
	 */
	private ?string $author = null;
	
	/**
	 * ISBN (öffentlich)
	 * 
	 * @Accessor(getter="get_isbn")
	 */
	private ?string $isbn = null;
	
	/**
	 * Sprache des Buches nach ISO 6393 (öffentlich)
	 * 
	 * @Accessor(getter="language")
	 */
	private Language $language = null;

	/**
	 * Get the value of author
	 */
	public function get_author()
	{
		return $this->author;
	}

	/**
	 * Set the value of author
	 *
	 * @return  self
	 */
	public function set_author($author)
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * Get the value of isbn
	 */
	public function get_isbn()
	{
		return $this->isbn;
	}

	/**
	 * Set the value of isbn
	 *
	 * @return  self
	 */
	public function set_isbn($isbn)
	{
		$this->isbn = $isbn;

		return $this;
	}

	/**
	 * Get the value of language
	 */
	public function get_language()
	{
		return $this->language;
	}

	/**
	 * Set the value of language
	 *
	 * @return  self
	 */
	public function set_language($language)
	{
		$this->language = $language;

		return $this;
	}
}
