<?php
declare(strict_types=1);

namespace App\Models\Traits;

trait MainModelTrait
{
	private readonly ?string $id;
	private readonly ?string $rev;
	
	public function get_id(): ?string {
		return $this->id;
	}
	
	public function get_rev(): ?string {
		return $this->rev;
	}
}

