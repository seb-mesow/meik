<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\Revisionable;
use App\Models\Interfaces\StringIdentifiable;
use App\Models\Traits\RevisionableTrait;
use App\Models\Traits\StringIdentifiableTrait;
use RuntimeException;
use stdClass;

class Image implements StringIdentifiable, Revisionable
{
	use StringIdentifiableTrait;
	use RevisionableTrait;
	
	private string $description;
	private bool $is_public;
	private ?int $image_width;
	private ?int $image_height;
	private ?int $thumbnail_width;
	private ?int $thumbnail_height;
	
	/**
	 * Wenn für das Image bereits Attactments hinterlegt sind,
	 * dann müssen wir diese auch im neu zu sendenden Doc angeben, damit sie erhalten bleiben.
	 * Nur für das Repository als "Durchlaufposten" relevant.
	 */
	private ?stdClass $attachments;
	
	public function __construct(
		string $description = '',
		bool $is_public = false,
		?int $image_width = null,
		?int $image_height = null,
		?int $thumbnail_width = null,
		?int $thumbnail_height = null,
		?stdClass $attachments = null,
		?string $id = null,
		?string $rev = null,
	) {
		$this->description = $description;
		$this->is_public = $is_public;
		$this->image_width = $image_width;
		$this->image_height = $image_height;
		$this->thumbnail_width = $thumbnail_width;
		$this->thumbnail_height = $thumbnail_height;
		
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
	 * @throws RuntimeException
	 * @return int
	 */
	public function get_image_width(): int {
		if ($this->image_width === null) {
			throw new RuntimeException('No image width set');
		}
		return $this->image_width;
	}
	
	/**
	 * @throws RuntimeException
	 * @return int
	 */
	public function get_image_height(): int {
		if ($this->image_height === null) {
			throw new RuntimeException('No image height set');
		}
		return $this->image_height;
	}
	
	/**
	 * @throws RuntimeException
	 * @return int
	 */
	public function get_thumbnail_width(): int {
		if ($this->thumbnail_width === null) {
			throw new RuntimeException('No thumbnail width set');
		}
		return $this->thumbnail_width;
	}
	
	/**
	 * @throws RuntimeException
	 * @return int
	 */
	public function get_thumbnail_height(): int {
		if ($this->thumbnail_height === null) {
			throw new RuntimeException('No thumbnail height set');
		}
		return $this->thumbnail_height;
	}
	
	public function get_nullable_image_width(): ?int {
		return $this->image_width;
	}
	
	public function get_nullable_image_height(): ?int {
		return $this->image_height;
	}
	
	public function get_nullable_thumbnail_width(): ?int {
		return $this->thumbnail_width;
	}
	
	public function get_nullable_thumbnail_height(): ?int {
		return $this->thumbnail_height;
	}
	
	/**
	 * Nur für das Repository als "Durchlaufposten" relevant.
	 * @return ?stdClass
	 */
	public function get_attachments(): ?stdClass {
		return $this->attachments;
	}
}
