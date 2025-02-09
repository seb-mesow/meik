<?php

declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Enum\Currency;
use App\Models\Enum\KindOfAcquistion;
use App\Models\Enum\KindOfProperty;
use App\Models\Enum\PreservationState;
use App\Models\Exhibit;
use App\Models\Parts\AcquisitionInfo;
use App\Models\Parts\FreeText;
use App\Models\Parts\Price;
use App\Repository\ExhibitRepository;
use App\Service\ExhibitService;
use App\Service\WordService;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExhibitAJAXController extends Controller
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly ExhibitService $exhibit_service,
		private readonly WordService $word_service
	) {}

	public function create(Request $request): JsonResponse
	{
		$inventory_number = (string) $request->input('inventory_number');
		$name = (string) $request->input('name');
		$short_description = (string) $request->input('short_description');
		$manufacturer = (string) $request->input('manufacturer');
		
		$exhibit = new Exhibit(
			inventory_number: $inventory_number,
			name: $name,
			place_id: '',
			rubric_id: '',
			connected_exhibit_ids: [],
			short_description: $short_description,
			manufacturer: $manufacturer,
			manufacture_date: '9999-12-31',
			preservation_state: PreservationState::FULLY_FUNCTIONAL,
			original_price: new Price(amount: 0, currency: Currency::EUR),
			current_value: 99999,
			acquisition_info: new AcquisitionInfo(
				date: Carbon::create(year: 2025, month: 2, day: 8),
				source: 'HERKUNFT',
				kind: KindOfAcquistion::FIND,
				purchasing_price: 99999,
			),
			kind_of_property: KindOfProperty::LOAN,
		);
		$this->exhibit_repository->insert($exhibit);
		return response()->json($exhibit->get_id());
	}
	
	public function update(Request $request, int $exhibit_id): void
	{
		$inventory_number = (string) $request->input('inventory_number');
		$name = (string) $request->input('name');
		$short_description = (string) $request->input('short_description');
		$manufacturer = (string) $request->input('manufacturer');
		
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->set_inventory_number($inventory_number);
		$exhibit->set_name($name);
		$exhibit->set_short_description($short_description);
		$exhibit->set_manufacturer($manufacturer);
		$this->exhibit_repository->update($exhibit);
		//sleep(5); // TODO entfernen
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

	public function get_qr_code(string $exhibit_id)
	{
		$text = $exhibit_id;
		$qrCode = new QrCode($text);

		// Create a new writer instance
		$writer = new PngWriter();
		$result = $writer->write($qrCode);

		return response($result->getString(), 200)->header('Content-Type', 'image/png');
	}

	public function get_tiles_paginated(Request $request): JsonResponse
	{
		$rubric_id = (string) $request->query('rubric_id');
		$page_number = (int) $request->query('page_number');
		
		if ($rubric_id === '') {
			$selectors = [];
		} else {
			$selectors = [
				'rubric_id' =>  [
					'$eq' => $rubric_id
				]
			];
		};
		
		$exhibits_json = $this->exhibit_service->determinate_tiles_props(page_number: $page_number, selectors: $selectors);
		
		return response()->json( $exhibits_json);
	}

	public function get_data_sheet(int $exhibit_id)
	{
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		return $this->word_service->get_data_sheet($exhibit);
	}
}
