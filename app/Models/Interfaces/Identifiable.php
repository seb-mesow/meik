<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface Identifiable
{
	public function get_nullable_id(): ?int;
	
	public function get_id(): int;
}
