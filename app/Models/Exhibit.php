<?php
declare(strict_types=1);

namespace App\Models;

use App;
use App\Models\Enum\KindOfProperty;
use App\Models\Enum\PreservationState;
use App\Models\Interfaces\IntIdentifiable;
use App\Models\Interfaces\Revisionable;
use App\Models\Parts\AcquisitionInfo;
use App\Models\Parts\DeviceInfo;
use App\Models\Parts\BookInfo;
use App\Models\Parts\Price;
use App\Models\Parts\FreeText;
use App\Models\Traits\IntIdentifiableTrait;
use App\Models\Traits\RevisionableTrait;
use App\Util\PartialDateValidator;
use App\Exceptions\InvalidPartialDateString;
use OutOfBoundsException;
use RuntimeException;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;

#[ExclusionPolicy("all")]
class Exhibit implements IntIdentifiable, Revisionable
{
	use IntIdentifiableTrait;
	use RevisionableTrait;
	
	#region Attribute
	
	/**
	 * Inventarnummer (intern)
	 * 
	 * @Accessor(getter="get_inventor_number")
	 */
	#[Expose]
	private string $inventory_number;
	
	/**
	 * Name/Titel (öffentlich)
	 * 
	 * @Accessor(getter="get_name")
	 */
	#[Expose]
	private string $name;
	
	/**
	 * Kurzbeschreibung (öffentlich)
	 */
	#[Expose]
	private string $short_description;
	
	/**
	 * Rubrik (öffentlich)
	 * 
	 * Daraus ergibt sich die Kategorie
	 */
	
	/**
	 * bei Geräte: Hersteller (öffentlich)
	 * 
	 * bei Bücher: Verlag (öffentlich)
	 * 
	 * @Accessor(getter="get_manufacturer") 
	 */
	#[Expose]
	private string $manufacturer;

	/**
	 * bei Geräten: Baujahr des konkreten Exponates (öffentlich)
	 * 
	 * bei Büchern: Erscheinungsjahr der konkreten Auflage/Jahr des Druckes (öffentlich)
	 * 
	 * @Accessor(getter="get_year_of_construction")
	 */
	#[Expose]
	private string $manufacture_date;
	
	/**
	 * Zustand (intern)
	 */
	private PreservationState $preservation_state;
	 
	/**
	 * Originalpreis in historischer Währung (öffentlich)
	 * 
	 * @Accessor(getter="get_original_price") 
	 */
	private Price $original_price;

	/**
	 * Zeitwert in Cent (intern)
	 * 
	 * @Accessor(getter="get_current_value") 
	 */
	private int $current_value = 0;
	
	/**
	 * Zugangsinformationen (meist intern)
	 */
	private AcquisitionInfo $acquisition_info;
	
	/**
	 * Art des Besitzes (öffentlich)
	 */
	private KindOfProperty $kind_of_property;
	
	/**
	 * bei Geräten: Geräteinformationen (meist öffentlich)
	 */
	private ?DeviceInfo $device_info;
	
	/**
	 * bei Büchern: Buchinformationen (meist öffentlich)
	 */
	private ?BookInfo $book_info;
	
	/**
	 * Platz (öffentlich)
	 * 
	 * daraus ergibt sich der Standort (Location)
	 * 
	 * @Accessor(getter="get_place_id")
	 */
	#[Expose]
	private string $place_id;
	
	/**
	 * in Verbindung stehende Exponate (öffentlich)
	 * 
	 * @var int[]
	 * 
	 * @Accessor(getter="get_connected_exhibit_ids") 
	 */
	#[Expose]
	private array $connected_exhibit_ids = [];
	
	/**
	 * Freitexte (teils öffentlich, teils intern)
	 * 
	 * @var FreeText[]
	 * 
	 * @Accessor(getter="get_free_texts") 
	 */
	#[Expose]
	#[SerializedName('freetexts')]
	private array $free_texts;
	
	#endregion
	
	#region constructor
	
	/**
	 *  @Accessor(getter="get_rubric_id") 
	 */
	private string $rubric_id;
	
	public function __construct(
		string $inventory_number,
		string $name,
		string $short_description,
		string $manufacturer,
		string $manufacture_date,
		PreservationState $preservation_state,
		Price $original_price,
		int $current_value,
		AcquisitionInfo $acquisition_info,
		KindOfProperty $kind_of_property,
		?DeviceInfo $device_info = null,
		?BookInfo $book_info = null,
		string $place_id,
		array $connected_exhibit_ids,
		array $free_texts = [],
		string $rubric_id,
		int|null $id = null,
		?string $rev = null
	) {
		$this->id = $id;
		$this->rev = $rev;
		
		$this->inventory_number = $inventory_number;
		$this->name = $name;
		$this->short_description = $short_description;
		$this->manufacturer = $manufacturer;
		$this->set_manufacture_date($manufacture_date);
		$this->preservation_state = $preservation_state;
		$this->original_price = $original_price;
		$this->current_value = $current_value;
		$this->acquisition_info = $acquisition_info;
		$this->kind_of_property = $kind_of_property;
		if ($device_info) {
			$this->set_device_info($device_info);
		} else {
			$this->set_book_info($book_info);
		}
		$this->connected_exhibit_ids = $connected_exhibit_ids;
		$this->place_id = $place_id;
		$this->free_texts = $free_texts;
		$this->rubric_id = $rubric_id;
	}
	
	#endregion
	
	#region Getter und Setter
	
	public function get_inventory_number(): string {
		return $this->inventory_number;
	}
	
	public function set_inventory_number(string $inventory_number): void {
		$this->inventory_number = $inventory_number;
	}

	public function get_name(): string {
		return $this->name;
	}

	public function set_name(string $name): void {
		$this->name = $name;
	}
	
	public function get_short_description(): string {
		return $this->short_description;
	}

	public function set_short_description(string $short_description): void {
		$this->short_description = $short_description;
	}

	public function get_manufacturer(): string {
		return $this->manufacturer;
	}

	public function set_manufacturer(string $manufacturer): void {
		$this->manufacturer = $manufacturer;
	}
	
	public function get_manufacture_date(): string {
		return $this->manufacture_date;
	}
	
	/**
	 * @throws InvalidPartialDateString
	 */
	public function set_manufacture_date(string $manufacture_date): void {
		App::make(PartialDateValidator::class)->validate_string($manufacture_date);
		$this->manufacture_date = $manufacture_date;
	}
	
	public function get_preservation_state(): PreservationState {
		return $this->preservation_state;
	}
	
	public function set_preservation_state(PreservationState $preservation_state): void {
		$this->preservation_state = $preservation_state;
	}
	
	
	public function get_original_price(): Price {
		return $this->original_price;
	}

	public function set_original_price(Price $original_price): void {
		$this->original_price = $original_price;
	}
	
	public function get_current_value(): int {
		return $this->current_value;
	}

	public function set_current_value(int $current_value): void {
		$this->current_value = $current_value;
	}
	
	public function get_acquistion_info(): AcquisitionInfo {
		return $this->acquisition_info;
	}

	public function set_acquistion_info(AcquisitionInfo $acquistion_info): void {
		$this->acquistion_info = $acquistion_info;
	}
	
	public function get_kind_of_property(): KindOfProperty {
		return $this->kind_of_property;
	}

	public function set_kind_of_property(KindOfProperty $kind_of_property): void {
		$this->kind_of_property = $kind_of_property;
	}
	
	public function is_device(): bool {
		return $this->device_info !== null;
	}
	
	public function is_book(): bool {
		return $this->book_info !== null;
	}
	
	public function get_device_info(): DeviceInfo {
		return $this->device_info;
	}

	public function set_device_info(DeviceInfo $device_info): void {
		$this->device_info = $device_info;
		$this->book_info = null;
	}
	
	public function get_book_info(): BookInfo {
		return $this->book_info;
	}

	public function set_book_info(BookInfo $book_info): void {
		$this->book_info = $book_info;
		$this->device_info = null;
	}
	
	public function get_place_id(): string{
		return $this->place_id;
	}
	
	public function set_place_id(string $place_id): void{
		$this->place_id = $place_id;
	}

	/**
	 * @return int[]
	 */
	public function get_connected_exhibit_ids(): array {
		return $this->connected_exhibit_ids;
	}
	
	/**
	 * @param int[] $connected_exhibit_ids
	 */
	public function set_connected_exhibit_ids(array $connected_exhibit_ids): void{
		$this->connected_exhibit_ids = $connected_exhibit_ids;
	}
	
	#endregion
	
	#region Freitexte
	
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
	 * @return int[]
	 */
	public function determinate_indices_order(): array {
		return array_map(static fn (FreeText $free_text): int => $free_text->get_id(), $this->free_texts);
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
	
	#endregion
}
