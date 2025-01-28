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
	private string $authors;
	
	/**
	 * ISBN (öffentlich)
	 * 
	 * @Accessor(getter="get_isbn")
	 */
	private string $isbn;
	
	/**
	 * Sprache des Buches nach ISO 6393 (öffentlich)
	 * 
	 * @Accessor(getter="language")
	 */
	private Language $language;

	public function __construct(
		string $authors,
		string $isbn,
		Language $language,
	) {
		$this->authors = $authors;
		$this->isbn = $isbn;
		$this->language = $language;
	}
	
	public function get_authors(): string
	{
		return $this->authors;
	}

	public function set_authors(string $authors): void
	{
		$this->authors = $authors;
	}

	public function get_isbn(): string
	{
		return $this->isbn;
	}

	public function set_isbn(string $isbn): void
	{
		$this->isbn = $isbn;
	}

	public function get_language(): Language
	{
		return $this->language;
	}

	public function set_language(Language $language): void
	{
		$this->language = $language;
	}
}
