<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface Identifiable
{
	public function get_nullable_id(): ?string;
	
	public function get_id(): string;
}
