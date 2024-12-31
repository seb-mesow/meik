<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface StringIdentifiable
{
	public function get_nullable_id(): ?string;
	
	public function get_id(): string;
	
		
	/** 
	 * kann dazu beitragen es sich zu ersparen
	 * in `insert()`-Funktionen von Repositories 
	 * ein neues Model-Objekt zu erstellen.
	 */
	public function set_id(string $id): void;
}
