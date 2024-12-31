<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface Revisionable
{
	public function get_nullable_rev(): ?string;
	
	public function get_rev(): string;
	
	/** 
	 * kann dazu beitragen es sich zu ersparen
	 * in `insert()`- und `update()`-Funktionen von Repositories 
	 * ein neues Model-Objekt zu erstellen.
	 */
	public function set_rev(string $new_rev): void;
}
