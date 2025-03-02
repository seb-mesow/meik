<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\IntIdentifiable;
use App\Models\Interfaces\Revisionable;
use App\Models\Traits\IntIdentifiableTrait;
use App\Models\Traits\RevisionableTrait;
use OutOfBoundsException;
use RuntimeException;

/**
 * ID = Exhibit-ID
 */
class ImageOrder implements IntIdentifiable, Revisionable
{
	use RevisionableTrait;
	use IntIdentifiableTrait;
	
	/**
	 * @var string[]
	 */
	private array $image_ids;
	
	/**
	 * @param $id must have an ID equal to an exhibit ID
	 * @param string[] $image_ids
	 */
	public function __construct(
		int $id,
		array $image_ids = [],
		?string $rev = null,
	) {
		$this->image_ids = $image_ids;
		$this->id = $id;
		$this->rev = $rev;
	}
	
	/**
	 * @return string[]
	 */
	public function get_image_ids(): array {
		return $this->image_ids;
	}
	
	/**
	 * inserts an image ID at the specified index
	 * 
	 * The indices are 0-based.
	 * The indices of subsequent free texts are increased by one
	 */
	public function insert_image_id(string $image_id, int $index): void {
		if ($index < 0) {
			throw new OutOfBoundsException("The index must be non-negative.");
		}
		if ($index > count($this->image_ids)) {
			throw new OutOfBoundsException("The index must be less than or equal to the current count of images.");
		}
		// From php.net:
		// "Note: If replacement is not an array, it will be typecast to one (i.e. (array) $replacement).
		// This may result in unexpected behavior when using an object or null replacement."
		array_splice($this->image_ids, $index, 0, [$image_id]);
	}
	
	/**
	 * @throws RuntimeException
	 */
	public function replace_image_id(string $old_image_id, string $new_image_id): void {
		foreach ($this->image_ids as $index => $existent_image_id) {
			if ($existent_image_id === $old_image_id) {
				$this->image_ids[$index] = $new_image_id;
				return;
			}
		}
		throw new RuntimeException("Image ID not found");
	}
	
	/**
	 * @throws RuntimeException
	 */
	public function remove_image_id(string $image_id): void {
		foreach ($this->image_ids as $index => $existent_image_id) {
			if ($existent_image_id === $image_id) {
				array_splice($this->image_ids, $index, 1);
				return;
			}
		}
		throw new RuntimeException("Image ID not found");
	}
	
	/**
	 * moves an image ID to the specified index
	 * 
	 * The indices are 0-based.
	 * The indices of subsequent free texts are increased by one
	 */
	public function move_image_id(string $image_id, int $new_index): void {
		foreach ($this->image_ids as $index => $existent_image_id) {
			if ($existent_image_id === $image_id) {
				array_splice($this->image_ids, $index, 1);
				array_splice($this->image_ids, $new_index, 0, [$image_id]);
				return;
			}
		}
		throw new RuntimeException("Image ID not found");
	}
	
	/**
	 * @param string[] $new_ids_order
	 * @return void
	 */
	public function set_image_order_ids(array $new_ids_order): void {
		foreach($this->image_ids as $existent_image_id) {
			if (!in_array($existent_image_id, $new_ids_order)) {
				throw new RuntimeException('new image id order missed existent ID ' . $existent_image_id);
			}
		}
		foreach($new_ids_order as $new_image_id) {
			if (!in_array($new_image_id, $this->image_ids)) {
				throw new RuntimeException('new image id order has surplus ID ' . $new_image_id);
			}
		}
		$this->image_ids = $new_ids_order;
	}
}

