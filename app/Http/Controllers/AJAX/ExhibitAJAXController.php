<?php

declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
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
use App\Repository\ExhibitRepository;
use App\Service\ExhibitService;
use App\Service\BasicScriptService;
use App\Service\WordService;
use App\Util\DateTimeUtil;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExhibitAJAXController extends Controller
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly ExhibitService $exhibit_service,
		private readonly BasicScriptService $basic_script_service,
		private readonly WordService $word_service,
		private readonly DateTimeUtil $date_time_util,
	) {}

	private function create_or_update(Request $request, ?Exhibit $exhibit = null): Exhibit
	{
		// ermittle Eingabewerte
		$inventory_number = (string) $request->input('inventory_number');
		$name = (string) $request->input('name');
		$short_description = (string) $request->input('short_description');
		$manufacturer = (string) $request->input('manufacturer');
		$manufacture_date = (string) $request->input('manufacture_date');
		$preservation_state_id = (string) $request->input('preservation_state_id');
		$original_price_arr = $request->input('original_price');
		$current_value = $request->input('current_value');
		$acquisition_info_arr = $request->input('acquisition_info');
		$kind_of_property_id = (string) $request->input('kind_of_property_id');
		$device_info_arr = $request->input('device_info'); // alle Kind-Elemente sind z.Z. Strings.
		$book_info_arr = $request->input('book_info'); // alle Kind-Elemente sind z.Z. Strings.
		$place_id = (string) $request->input('place_id');
		$rubric_id = (string) $request->input('rubric_id');
		$connected_exhibit_ids = (array) $request->input('conntected_exhibit_ids');
		
		// wandle Eingabewerte um
		$preservation_state = PreservationState::from($preservation_state_id);
		if (is_integer($original_price_arr['amount']) && (is_string($original_price_arr['currency_id']) && $original_price_arr['currency_id'] !== '')) {
			$original_price = new Price(
				amount: (int) $original_price_arr['amount'],
				currency: Currency::from((string) $original_price_arr['currency_id']),
			);
		} else {
			$original_price = null;
		}
		$acquisition_info = new AcquisitionInfo(
			date: $this->date_time_util->parse_iso_date((string) $acquisition_info_arr['date']),
			source: (string) $acquisition_info_arr['source'],
			kind: (is_string($acquisition_info_arr['kind_id']) && $acquisition_info_arr['kind_id'] !== '') ? KindOfAcquisition::from($acquisition_info_arr['kind_id']) : null,
			purchasing_price: is_integer($acquisition_info_arr['purchasing_price']) ? $acquisition_info_arr['purchasing_price'] : null,
		);
		$kind_of_property = KindOfProperty::from($kind_of_property_id);
		if (is_array($device_info_arr)) {
			$device_info = new DeviceInfo(
				manufactured_from_date: (string) $device_info_arr['manufactured_from_date'],
				manufactured_to_date: (string) $device_info_arr['manufactured_to_date']
			);
		} else {
			$device_info = null;
		}
		if (is_array($book_info_arr)) {
			$book_info = new BookInfo(
				authors: (string) $book_info_arr['authors'],
				isbn: (string) $book_info_arr['isbn'],
				language: Language::from((string) $book_info_arr['language_id']),
			);
		} else {
			$book_info = null;
		}
		
		if ($exhibit) {
			// bestehendes Exhibit updaten
			$exhibit->set_inventory_number($inventory_number);
			$exhibit->set_name($name);
			$exhibit->set_short_description($short_description);
			$exhibit->set_manufacturer($manufacturer);
			$exhibit->set_manufacture_date($manufacture_date);
			$exhibit->set_preservation_state($preservation_state);
			$exhibit->set_original_price($original_price);
			$exhibit->set_current_value($current_value);
			$exhibit->set_acquisition_info($acquisition_info);
			$exhibit->set_kind_of_property($kind_of_property);
			if ($device_info) {
				$exhibit->set_device_info($device_info);
			}
			if ($book_info) {
				$exhibit->set_book_info($book_info);
			}
			$exhibit->set_place_id($place_id);
			$exhibit->set_connected_exhibit_ids($connected_exhibit_ids);
			$exhibit->set_rubric_id($rubric_id);
		} else {
			// neues Exhibit erzeugen
			$exhibit = new Exhibit(
				inventory_number: $inventory_number,
				name: $name,
				short_description: $short_description,
				manufacturer: $manufacturer,
				manufacture_date: $manufacture_date,
				preservation_state: $preservation_state,
				original_price: $original_price,
				current_value: $current_value,
				acquisition_info: $acquisition_info,
				kind_of_property: $kind_of_property,
				device_info: $device_info,
				book_info: $book_info,
				place_id: $place_id,
				rubric_id: $rubric_id,
				connected_exhibit_ids: $connected_exhibit_ids,
			);
		}
		
		return $exhibit;
	}
	
	public function create(Request $request): JsonResponse
	{
		$exhibit = $this->create_or_update($request);
		$this->exhibit_repository->insert($exhibit);
		return response()->json($exhibit->get_id());
	}
	
	public function update(Request $request, int $exhibit_id): void
	{
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit = $this->create_or_update($request, $exhibit);
		$this->exhibit_repository->update($exhibit);
	}

	public function create_free_text(Request $request, int $exhibit_id): JsonResponse
	{
		$index = $request->input('index');
		$heading = $request->input('val.heading.val');
		$html = $request->input('val.html.val');
		$is_public = $request->input('val.is_public.val');

		$free_text = new FreeText(
			heading: $heading,
			html: $html,
			is_public: $is_public,
		);

		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->insert_free_text($free_text, $index);
		$this->exhibit_repository->update($exhibit);
		$free_text_id = $free_text->get_id();
		$indices_order = $exhibit->determinate_indices_order();

		return response()->json([
			'id' => $free_text_id,
			'indices_order' => $indices_order
		]);
	}

	public function update_free_text(Request $request, int $exhibit_id, int $free_text_id): void
	{
		$heading = $request->input('val.heading.val');
		$html = $request->input('val.html.val');
		$is_public = $request->input('val.is_public.val');

		$free_text = new FreeText(
			id: $free_text_id,
			heading: $heading,
			html: $html,
			is_public: $is_public,
		);

		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->update_free_text($free_text);
		$this->exhibit_repository->update($exhibit);
	}

	public function delete_free_text(Request $request, int $exhibit_id, int $free_text_id): JsonResponse
	{
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->remove_free_text($free_text_id);
		$this->exhibit_repository->update($exhibit);
		$indices_order = $exhibit->determinate_indices_order();
		return response()->json($indices_order);
	}

	public function move_free_text(Request $request, int $exhibit_id, int $free_text_id): JsonResponse
	{
		$new_index = $request->input();
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->move_free_text($free_text_id, $new_index);
		$this->exhibit_repository->update($exhibit);
		$indices_order = $exhibit->determinate_indices_order();
		return response()->json($indices_order);
	}

	public function tiles_query(Request $request): JsonResponse
	{
		// TODO enable search by location_id
		$rubric_id = $request->query('rubric_id');
		$page_number = $request->query('page_number');
		$count_per_page = $request->query('count_per_page');
		
		$rubric_id = is_string($rubric_id) ? trim($rubric_id) : null;
		$rubric_id = $rubric_id === '' ? null : $rubric_id;
		$page_number = is_string($page_number) ? trim($page_number) : null;
		$page_number = $page_number === '' ? null : $page_number;
		$page_number = is_numeric($page_number) ? (int) $page_number : null;
		$count_per_page = is_string($count_per_page) ? trim($count_per_page) : null;
		$count_per_page = $count_per_page === '' ? null : $count_per_page;
		$count_per_page = is_numeric($count_per_page) ? (int) $count_per_page : null;
		
		assert(($page_number === null) == ($count_per_page === null));
		
		if ($rubric_id === null) {
			$selectors = [];
		} else {
			$selectors = [
				'rubric_id' =>  [
					'$eq' => $rubric_id
				]
			];
		};
		
		$exhibits_json = $this->exhibit_service->determinate_tiles_props(
			page_number: $page_number,
			count_per_page: $count_per_page,
			selectors: $selectors,
		);
		
		return response()->json( $exhibits_json);
	}
	
	public function get_qr_code(string $exhibit_id)
	{
		$text = $exhibit_id;
		$qrCode = new QrCode($text);

		// Create a new writer instance
		$writer = new PngWriter();
		$result = $writer->write($qrCode);

		return response($result->getString(), 200)->header('Content-Type', 'image/png');
	}
	
	public function get_qr_code_basic_script(int $exhibit_id): BinaryFileResponse
	{
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$ret = $this->basic_script_service->create_qr_code_basic_script($exhibit);
		return response()->download(
			file: $ret['tmp_file_path'],
			name: $ret['user_file_name'],
			headers: [
				'Content-Type' => $ret['content_type'] . '; charset=' . $ret['charset'],
			],
		)->deleteFileAfterSend();
	}
	
	public function get_data_sheet(int $exhibit_id)
	{
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		return $this->word_service->get_data_sheet($exhibit);
	}

	public function connected_exhibits_query(Request $request)
	{
		$criteria = $request->query('criteria');
		assert(is_string($criteria));
		$criteria = trim($criteria);
		
		// Ersetze allen Zwischen-Whitespace durch eine Regex für mehrere Whitespace-Zeichen.
		$criteria = mb_ereg_replace('\s+', '\\s+', $criteria);
		
		// Exhibits, welche bereits in der Form enthalten sind, werden erst im Frontend weggefiltert.
		// Dafür ist die DB-Abfrage einfacher und schneller.
		
		$selectors = [
			'name' => [
				'$regex' => '(?i)' . $criteria,
			]
		];
		
		$exhibits = $this->exhibit_repository->query($selectors);
		
		$exhibits_json = array_map(static fn(Exhibit $exhibit): array => [
			'id' => $exhibit->get_id(),
			'name' => $exhibit->get_name(),
		], $exhibits);
		
		return $exhibits_json;
	}
}
