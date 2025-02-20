<?php
declare(strict_types=1);

namespace App\Repository;

use App\Models\Enum\Currency;
use App\Models\Enum\KindOfAcquisition;
use App\Models\Enum\KindOfProperty;
use App\Models\Enum\Language;
use App\Models\Enum\PreservationState;
use App\Models\Exhibit;
use App\Models\Parts\AcquisitionInfo;
use App\Models\Parts\BookInfo;
use App\Models\Parts\DeviceInfo;
use App\Models\Parts\FreeText;
use App\Models\Parts\Price;
use App\Repository\Traits\IntIdRepositoryTrait;
use Illuminate\Support\Carbon;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use stdClass;

/**
 * Die _id wird bei neuen Docs sofort gesetzt.
 * @phpstan-type ExhibitDoc object{
 *     _id: string,
 *     _rev?: string,
 *     inventory_number: string,
 *     name: string,
 *     short_description: string,
 *     manufacturer: string,
 *     manufacture_date: string,
 *     preservation_state: string,
 *     original_price: PriceDoc,
 *     current_value: int|null,
 *     acquisition_info: AcquisitionInfoDoc,
 *     kind_of_property: string,
 *     device_info?: DeviceInfoDoc,
 *     book_info?: BookInfoDoc,
 *     place_id: int,
 *     rubric_id: string,
 *     connected_exhibit_ids: int[],
 *     free_texts: FreeTextDoc[]
 * }
 * 
 * @phpstan-type PriceDoc object{
 *    amount: int|null,
 *    currency: string
 * }
 * 
 * @phpstan-type AcquisitionInfoDoc object{
 *    date: string,
 *    source: string,
 *    kind: string,
 *    purchasing_price: int|null
 * }
 * 
 * @phpstan-type DeviceInfoDoc object{
 *    manufactured_from_date: string,
 *    manufactured_to_date: string
 * }
 * 
 * @phpstan-type BookInfoDoc object{
 *    authors: string,
 *    isbn: string,
 *    language: string
 * }
 * 
 * Die _id wird bei neuen Docs sofort gesetzt.
 * @phpstan-type FreeTextDoc object{
 *     _id: int,
 *     heading: string,
 *     html: string,
 *     is_public: bool
 * }
 * 
 * @phpstan-type MetaDoc object{
 *     next_id: int,
 *     free_text: object {
 *         next_id: int
 *     }
 * }
 */
final class ExhibitRepository
{
	use IntIdRepositoryTrait;

	public const string MODEL_TYPE_ID = "exhibit";
	private const string ISO_8601_DATE_FORMAT = 'Y-m-d';
	private const string ISO_8601_DATETIME_FORMAT = 'Y-m-d\\TH:i:sp';
	
	public function __construct(
		CouchClient $client
	) {
		$this->client = $client;
		$this->meta_doc = $this->get_meta_doc();
	}
	
	public function find(int $id): ?Exhibit
	{
		try {
			return $this->get($id);
		} catch (CouchNotFoundException $e) {
			return null;
		}
	}
	
	public function get(int $id): Exhibit
	{
		$doc_id = $this->determinate_doc_id_from_model_id($id);
		$exhibit_doc = $this->client->getDoc($doc_id);
		return $this->create_exhibit_from_doc($exhibit_doc);
	}
	
	/**
	 * @return Exhibit[]
	 */
	public function get_all(): array
	{
		$res = $this->client
			->limit(PHP_INT_MAX)
			->find([
			'_id' => [
				'$beginsWith' => self::ID_PREFIX
			],
		]);
		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): Exhibit {
			return $_this->create_exhibit_from_doc($doc);
		}, $res->docs);
	}

	/**
	 * ggf. ist irgendwann einmal noch total_count erforderlich
	 * 
	 * @return Exhibit[]
	 */
	public function get_paginated(int $page_number, int $count_per_page, array $additonal_selectors = []): array
	{
		$selectors = [
			'_id' => [
				'$beginsWith' => self::ID_PREFIX
			]
		];
		$selectors = array_merge($selectors, $additonal_selectors);

		$response = $this->client
			->limit($count_per_page)
			->skip($page_number * $count_per_page)
			->find([
				'$and' => [
					$selectors
				]
			])
			->docs;
			
		$_this = $this;
		$exhibits = array_map(static function (stdClass $doc) use ($_this): Exhibit {
			return $_this->create_exhibit_from_doc($doc);
		}, $response);
		
		return $exhibits;
	}
	
	/**
	 * @param array $selectors
	 * @return Exhibit[]
	 */
	public function get_by_selectors(array $selectors): array
	{
		$docs = $this->client->find(
			$selectors
		)->docs;

		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): Exhibit {
			return $_this->create_exhibit_from_doc($doc);
		}, $docs);
	}
	
	public function insert(Exhibit $exhibit): void
	{
		assert(!$exhibit->get_nullable_id());
		assert(!$exhibit->get_nullable_rev());
		$doc = $this->create_doc_from_exhibit($exhibit); // setzt neue ID
		$response = $this->client->storeDoc($doc);
		$exhibit->set_rev($response->rev);
	}

	public function update(Exhibit $exhibit): void
	{
		assert($exhibit->get_id());
		assert($exhibit->get_rev());
		$doc = $this->create_doc_from_exhibit($exhibit);
		$response = $this->client->storeDoc($doc);
		$exhibit->set_rev($response->rev);
	}

	public function remove(Exhibit $exhibit): void
	{
		assert($exhibit->get_id());
		assert($exhibit->get_rev());
		$delete_doc = $this->create_stub_doc_from_model($exhibit);
		$this->client->deleteDoc($delete_doc);
	}

	/**
	 * @return ExhibitDoc
	 */
	private function create_doc_from_exhibit(Exhibit $exhibit): stdClass
	{
		/** @var ExhibitDoc */
		$exhibit_doc = $this->create_stub_doc_from_model($exhibit);

		$exhibit_doc->inventory_number = $exhibit->get_inventory_number();
		$exhibit_doc->name = $exhibit->get_name();
		$exhibit_doc->short_description = $exhibit->get_short_description();
		$exhibit_doc->manufacturer = $exhibit->get_manufacturer();
		$exhibit_doc->manufacture_date = $exhibit->get_manufacture_date();
		$exhibit_doc->preservation_state = $exhibit->get_preservation_state()->get_id();
		
		$original_price = $exhibit->get_original_price();
		/** @var PriceDoc */
		$original_price_doc = new stdClass();
		$original_price_doc->amount = $original_price?->get_amount();
		$original_price_doc->currency = $original_price?->get_currency()->get_id();
		$exhibit_doc->original_price = $original_price_doc;
		
		$exhibit_doc->current_value = $exhibit->get_current_value();
		
		$acquisition_info = $exhibit->get_acquisition_info();
		/** @var AcquisitionInfoDoc */
		$acquisition_info_doc = new stdClass(); 
		$acquisition_info_doc->date = $acquisition_info->get_date()->format(self::ISO_8601_DATE_FORMAT);
		$acquisition_info_doc->source = $acquisition_info->get_source();
		$acquisition_info_doc->kind = $acquisition_info->get_kind()?->get_id() ?? '';
		$acquisition_info_doc->purchasing_price = $acquisition_info->get_purchasing_price();
		$exhibit_doc->acquisition_info = $acquisition_info_doc;
		
		$exhibit_doc->kind_of_property = $exhibit->get_kind_of_property()->get_id();
		
		if ($exhibit->is_device()) {
			$device_info = $exhibit->get_device_info();
			/** @var DeviceInfoDoc */
			$device_info_doc = new stdClass();
			$device_info_doc->manufactured_from_date = $device_info->get_manufactured_from_date();
			$device_info_doc->manufactured_to_date = $device_info->get_manufactured_to_date();
			$exhibit_doc->device_info = $device_info_doc;
			// $exhibit_doc->book_info = null
		} else {
			$book_info = $exhibit->get_book_info();
			/** @var BookInfoDoc */
			$book_info_doc = new stdClass();
			$book_info_doc->authors = $book_info->get_authors();
			$book_info_doc->isbn = $book_info->get_isbn();
			$book_info_doc->language = $book_info->get_language()->get_id();
			$exhibit_doc->book_info = $book_info_doc;
			// $exhibit_doc->device_info = null
		}
		
		$exhibit_doc->place_id = $exhibit->get_place_id();
		$exhibit_doc->rubric_id = $exhibit->get_rubric_id();
		$exhibit_doc->connected_exhibit_ids = $exhibit->get_connected_exhibit_ids();

		$_this = $this;
		$free_text_docs = array_map(static function (FreeText $free_text) use ($_this): stdClass {
			return $_this->create_doc_from_free_text($free_text);
		}, $exhibit->get_free_texts());
		$exhibit_doc->free_texts = $free_text_docs;

		return $exhibit_doc;
	}

	/**
	 * @param ExhibitDoc $exhibit_doc
	 */
	private function create_exhibit_from_doc(stdClass $exhibit_doc): Exhibit
	{
		/** @var AcquisitionInfoDoc */
		$acquisition_info_doc = $exhibit_doc->acquisition_info;
		$acquisition_info = new AcquisitionInfo(
			date: Carbon::createFromFormat(self::ISO_8601_DATE_FORMAT, $acquisition_info_doc->date),
			source: $acquisition_info_doc->source,
			kind: $acquisition_info_doc->kind ? KindOfAcquisition::from($acquisition_info_doc->kind) : null,
			purchasing_price: $acquisition_info_doc->purchasing_price,
		);
		
		/** @var PriceDoc */
		$original_price_doc = $exhibit_doc->original_price;
		if (is_integer( $original_price_doc->amount) && $original_price_doc->currency !== '') {
			$original_price = new Price(
				amount: $original_price_doc->amount,
				currency: Currency::from($original_price_doc->currency),
			);
		} else {
			$original_price = null;
		}
		
		if (property_exists($exhibit_doc, 'device_info')) {
			/** @var DeviceInfoDoc */
			$device_info_doc = $exhibit_doc->device_info;
			$device_info = new DeviceInfo(
				manufactured_from_date: $device_info_doc->manufactured_from_date,
				manufactured_to_date: $device_info_doc->manufactured_to_date,
			);
			$book_info = null;
		} else {
			/** @var BookInfoDoc */
			$book_info_doc = $exhibit_doc->book_info;
			$book_info = new BookInfo(
				authors: $book_info_doc->authors,
				isbn: $book_info_doc->isbn,
				language: Language::from($book_info_doc->language),
			);
			$device_info = null;
		}
		
		$_this = $this;
		$free_texts = array_map(static function (stdClass $free_text_doc) use ($_this): FreeText {
			return $_this->create_free_text_from_doc($free_text_doc);
		}, $exhibit_doc->free_texts);

		return new Exhibit(
			inventory_number: $exhibit_doc->inventory_number,
			name: $exhibit_doc->name,
			short_description: $exhibit_doc->short_description,
			manufacturer: $exhibit_doc->manufacturer,
			manufacture_date: $exhibit_doc->manufacture_date,
			preservation_state: PreservationState::from($exhibit_doc->preservation_state),
			original_price: $original_price,
			current_value: $exhibit_doc->current_value,
			acquisition_info: $acquisition_info,
			kind_of_property: KindOfProperty::from($exhibit_doc->kind_of_property),
			device_info: $device_info,
			book_info: $book_info,
			place_id: $exhibit_doc->place_id,
			connected_exhibit_ids: $exhibit_doc->connected_exhibit_ids,
			free_texts: $free_texts,
			rubric_id: $exhibit_doc?->rubric_id,
			id: $this->determinate_model_id_from_doc($exhibit_doc),
			rev: $exhibit_doc->_rev,
		);
	}

	/**
	 * @return FreeTextDoc
	 */
	private function create_doc_from_free_text(FreeText $free_text): stdClass
	{
		/** @var FreeTextDoc */
		$free_text_doc = $this->create_stub_sub_doc_from_sub_model($free_text, 'free_text');
		$free_text_doc->heading = $free_text->get_heading();
		$free_text_doc->html = $free_text->get_html();
		$free_text_doc->is_public = $free_text->get_is_public();
		return $free_text_doc;
	}

	/**
	 * @param FreeTextDoc $free_text_doc
	 */
	private function create_free_text_from_doc(stdClass $free_text_doc): FreeText
	{
		return new FreeText(
			id: (int) $free_text_doc->_id,
			heading: $free_text_doc->heading,
			html: $free_text_doc->html,
			is_public: $free_text_doc->is_public,
		);
	}
}
