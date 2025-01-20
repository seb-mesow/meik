<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\IntIdentifiable;
use App\Models\Interfaces\Revisionable;
use App\Models\Traits\IntIdentifiableTrait;
use App\Models\Traits\RevisionableTrait;
use DateTime;
use OutOfBoundsException;
use RuntimeException;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;


#[ExclusionPolicy("all")]
class Exhibit implements IntIdentifiable, Revisionable
{
	use IntIdentifiableTrait;
	use RevisionableTrait;
	
	/** @Accessor(getter="get_inventor_number") */
	#[Expose]
	private string $inventory_number;
	
	/** @Accessor(getter="get_name") */
	#[Expose]
	private string $name;

	/** @Accessor(getter="get_manufacturer") 
	 */
	#[Expose]
	private ?string $manufacturer = null;

	/** @Accessor(getter="get_year_of_construction") */
	#[Expose]
	private int $year_of_manufacture;

	/** @Accessor(getter="get_place_id") */
	#[Expose]
	private string $place_id;

	/** @Accessor(getter="get_aquiry_date") 
	 * @Type("DateTime")
	 */
	#[Expose]
	private ?DateTime $aquiry_date = null;

	/** 
	 * @var FreeText[]
	 * 
	 * @Accessor(getter="get_free_texts") 
	 */
	#[Expose]
	#[SerializedName('freetexts')]
	private array $free_texts;

	/** @Accessor(getter="get_connected_exhibits") 
	 */
	#[Expose]
	private ?array $connected_exhibits = [];

	/**
	 *  @Accessor(getter="get_original_price") 
	 */
	private ?price $original_price = null;

	/**
	 *  @Accessor(getter="get_current_value") 
	 */
	private ?float $current_value = 0;

	/**
	 *  @Accessor(getter="get_rubric_id") 
	 */
	private string $rubric_id;
	
	public function __construct(
		string $inventory_number,
		string $name,
		string $manufacturer,
		int $year_of_manufacture,
		string $place_id,
		array $free_texts = [],
		string $rubric_id,
		int|null $id = null,
		?string $rev = null
	) {
		$this->id = $id;
		$this->rev = $rev;
		
		$this->inventory_number = $inventory_number;
		$this->name = $name;
		$this->manufacturer = $manufacturer;
		$this->year_of_manufacture = $year_of_manufacture;
		$this->place_id = $place_id;
		$this->free_texts = $free_texts;
		$this->rubric_id = $rubric_id;
	}
		
	public function get_inventory_number(): string {
		return $this->inventory_number;
	}
	
	public function set_inventory_number(string $inventory_number): self {
		$this->inventory_number = $inventory_number;
		return $this;
	}

	public function get_name(): string {
		return $this->name;
	}

	public function set_name(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function get_manufacturer() {
		return $this->manufacturer;
	}

	public function set_manufacturer($manufacturer): self {
		$this->manufacturer = $manufacturer;
		return $this;
	}

	public function get_place_id(){
		return $this->place_id;
	}

	public function get_year_of_manufacture(): int {
		return $this->year_of_manufacture;
	}
	
	public function get_aquiry_date(){
		return $this->aquiry_date;
	}

	public function set_aquiry_date($aquiry_date){
		$this->aquiry_date = $aquiry_date;

		return $this;
	}

	/**
	 * @return FreeText[]
	 */
	public function get_free_texts(): array {
		return $this->free_texts;
	}
	
	/**
	 * @return int[]
	 */
	public function determinate_indices_order(): array {
		return array_map(static fn (FreeText $free_text): int => $free_text->get_id(), $this->free_texts);
	}
	
	/**
	 * @param FreeText[] $free_texts
	 */
	public function set_free_texts(array $free_texts): self {
		$this->free_texts = $free_texts;
		return $this;
	}
	
	/**
	 * inserts a free text at the specified index
	 * 
	 * The indices are 0-based.
	 * The indices of subsequent free texts are increased by one
	 */
	public function insert_free_text(FreeText $free_text, int $index): self {
		assert(!$free_text->get_id());
		if ($index < 0) {
			throw new OutOfBoundsException("The index must be non-negative.");
		}
		if ($index > count($this->free_texts)) {
			throw new OutOfBoundsException("The index must be less than or equal to the current count of free texts.");
		}
		// From php.net:
		// "Note: If replacement is not an array, it will be typecast to one (i.e. (array) $replacement).
		// This may result in unexpected behavior when using an object or null replacement."
		array_splice($this->free_texts, $index, 0, [$free_text]);
		return $this;
	}
	
	public function update_free_text(FreeText $new_free_text): self {
		assert($new_free_text->get_id());
		// if ($index < 0) {
		// 	throw new OutOfBoundsException("The index must be non-negative.");
		// }
		// if ($index >= count($this->free_texts)) {
		// 	throw new OutOfBoundsException("The index must be less than or equal to the current count of free texts.");
		// }
		foreach ($this->free_texts as $index => $existing_free_text) {
			if ($existing_free_text->get_id() === $new_free_text->get_id()) {
				$this->free_texts[$index] = $new_free_text;
			}
		}
		return $this;
	}
	
	public function remove_free_text(int $free_text_id): self {
		// if ($index < 0) {
		// 	throw new OutOfBoundsException("The index must be non-negative.");
		// }
		// if ($index >= count($this->free_texts)) {
		// 	throw new OutOfBoundsException("The index must be less than to the current count of free texts.");
		// }
		foreach ($this->free_texts as $index => $free_text) {
			if ($free_text->get_id() === $free_text_id) {
				array_splice($this->free_texts, $index, 1);
				return $this;
			}
		}
		throw new RuntimeException("No FreeText with the specified ID found");
	}
	
	/**
	 * moves a free text to the specified index
	 * 
	 * The indices are 0-based.
	 * The indices of subsequent free texts are increased by one
	 */
	public function move_free_text(int $free_text_id, int $new_index): self {
		foreach ($this->free_texts as $index => $free_text) {
			if ($free_text->get_id() === $free_text_id) {
				array_splice($this->free_texts, $index, 1);
				array_splice($this->free_texts, $new_index, 0, [$free_text]);
				return $this;
			}
		}
		throw new RuntimeException("No FreeText with the specified ID found");
	}
	
	public function get_connected_exhibits(){
		return $this->connected_exhibits;
	}

	public function set_connected_exhibits($connected_exhibits){
		$this->connected_exhibits = $connected_exhibits;

		return $this;
	}

	public function get_original_price(){
		return $this->original_price;
	}

	public function set_original_price($original_price){
		$this->original_price = $original_price;

		return $this;
	}

	public function get_current_value(){
		return $this->current_value;
	}

	public function set_current_value($current_value){
		$this->current_value = $current_value;

		return $this;
	}


	public function get_rubric_id()
	{
		return $this->rubric_id;
	}

	/**
	 * @return  self
	 */ 
	public function set_rubric_id($rubric_id)
	{
		$this->rubric_id = $rubric_id;

		return $this;
	}
}
