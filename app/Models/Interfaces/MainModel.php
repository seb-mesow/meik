<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface MainModel
{
	public function get_id(): ?string;
	
	public function get_rev(): ?string;
}
