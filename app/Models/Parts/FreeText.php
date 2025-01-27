<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\IntIdentifiable;
use App\Models\Traits\IntIdentifiableTrait;

class FreeText implements IntIdentifiable
{
	use IntIdentifiableTrait;
	
	/** @Accessor(getter="get_heading") */
	private string $heading;
	
	/** @Accessor(getter="get_html") */
	private string $html;
	
	/** @Accessor(getter="get_is_public") */
	private bool $is_public;

	public function __construct(
		string $heading,
		string $html,
		bool $is_public = false,
		int|null $id = null
	) {
		$this->id = $id;
		
		$this->heading = $heading;
		$this->html = $html;
		$this->is_public = $is_public;
	}
	
	public function get_heading(): string {
		return $this->heading;
	}

	public function set_heading(string $heading): self {
		$this->heading = $heading;
		return $this;
	}

	public function get_html(): string {
		return $this->html;
	}

	public function set_html(string $html): self {
		$this->html = $html;
		return $this;
	}

	public function get_is_public(): bool {
		return $this->is_public;
	}

	public function set_is_public(bool $is_public): self {
		$this->is_public = $is_public;
		return $this;
	}
}
