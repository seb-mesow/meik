<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\Revisionable;
use App\Models\Interfaces\StringIdentifiable;
use App\Models\Traits\RevisionableTrait;
use App\Models\Traits\StringIdentifiableTrait;
use stdClass;

class Image implements StringIdentifiable, Revisionable
{
	use StringIdentifiableTrait;
	use RevisionableTrait;
	
	private string $description;
	private bool $is_public;
	
	/**
	 * Wenn für das Image bereits Attactments hinterlegt sind,
	 * dann müssen wir diese auch im neu zu sendenden Doc angeben, damit sie erhalten blieben.
	 * Nur für das Repository als "Durchlaufposten" relevant.
	 */
	private ?stdClass $attachments;
	
	public function __construct(
		string $description = '',
		bool $is_public = false,
		?stdClass $attachments = null,
		?string $id = null,
		?string $rev = null
	) {
		$this->description = $description;
		$this->is_public = $is_public;
		$this->attachments = $attachments;
		$this->id = $id;
		$this->rev = $rev;
	}
	
	public function get_description(): string {
		return $this->description;
	}
	
	public function set_description(string $description): void {
		$this->description = $description;
	}
	
	public function get_is_public(): bool {
		return $this->is_public;
	}
	
	public function set_is_public(bool $is_public): void {
		$this->is_public = $is_public;
	}
	
	/**
	 * Nur für das Repository als "Durchlaufposten" relevant.
	 * @return ?stdClass
	 */
	public function get_attachments(): ?stdClass {
		return $this->attachments;
	}
}
