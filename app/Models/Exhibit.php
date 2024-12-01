<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use OutOfBoundsException;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

class Exhibit
{
	/** @Accessor(getter="get_id") */
	private readonly ?string $id;

	/** @Accessor(getter="get_rev") */
	private readonly ?string $rev;

	/** @Accessor(getter="get_inventor_number") */
	private string $inventory_number;
	
	/** @Accessor(getter="get_name") */
	private string $name;

	/** @Accessor(getter="get_manufacturer") 
	 */
	private ?string $manufacturer = null;

	/** @Accessor(getter="get_year_of_construction") */
	private ?string $year_of_construction = null;

	/** @Accessor(getter="get_place") */
	private ?string $place = null;

	/** @Accessor(getter="get_aquiry_date") 
	 * @Type("DateTime")
	 */
	private ?DateTime $aquiry_date = null;

	/** @Accessor(getter="get_free_texts") 
	 * @Serializer\SerializedName("freetexts")
	 */
	private array $free_texts;

	/** @Accessor(getter="get_connected_exhibits") 
	 */
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
	 * @param FreeText[] $free_texts
	 */
	public function __construct(
		string $inventory_number,
		string $name,
		string $manufacturer,
		array $free_texts = [],
		?string $id = null,
		?string $rev = null
	) {
		$this->id = $id;
		$this->rev = $rev;
		
		$this->inventory_number = $inventory_number;
		$this->name = $name;
		$this->manufacturer = $manufacturer;
		$this->free_texts = $free_texts;
	}
	
	public function get_id(): ?string {
		return $this->id;
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

	public function get_year_of_construction(){
		return $this->year_of_construction;
	}

	public function set_year_of_construction($year_of_construction){
		$this->year_of_construction = $year_of_construction;

		return $this;
	}

	public function get_place(){
		return $this->place;
	}

	public function set_place($place){
		$this->place = $place;

		return $this;
	}

	public function get_aquiry_date(){
		return $this->aquiry_date;
	}

	public function set_aquiry_date($aquiry_date){
		$this->aquiry_date = $aquiry_date;

		return $this;
	}

	public function get_rev(): ?string {
		return $this->rev;
	}
	
	/**
	 * @return FreeText[]
	 */
	public function get_free_texts(): array {
		return $this->free_texts;
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
	
	/**
	 * updates the free text at the specified index
	 * 
	 * The indices are 0-based.
	 */
	public function update_free_text(FreeText $free_text, int $index): self {
		if ($index < 0) {
			throw new OutOfBoundsException("The index must be non-negative.");
		}
		if ($index >= count($this->free_texts)) {
			throw new OutOfBoundsException("The index must be less than or equal to the current count of free texts.");
		}
		$this->free_texts[$index] = $free_text;
		return $this;
	}
	
	/**
	 * removes the free text at the specified index
	 *
	 * The indices of subsequent free texts are decreased by one
	 */
	public function remove_free_text(int $index): self {
		if ($index < 0) {
			throw new OutOfBoundsException("The index must be non-negative.");
		}
		if ($index >= count($this->free_texts)) {
			throw new OutOfBoundsException("The index must be less than to the current count of free texts.");
		}
		array_splice($this->free_texts, $index, 1);
		return $this;
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
}
