<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface Revisionable
{
	public function get_nullable_rev(): ?string;
	
	public function get_rev(): string;
}
