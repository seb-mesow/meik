<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface IntIdentifiable
{
	public function get_nullable_id(): ?int;
	
	public function get_id(): int;
	
	/** 
	 * kann dazu beitragen es sich zu ersparen
	 * in `insert()`-Funktionen von Repositories 
	 * ein neues Model-Objekt zu erstellen.
	 */
	public function set_id(int $id): void;
}
