<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Models\FreeText;
use App\Models\Rubric;
use App\Repository\ExhibitRepository;
use App\Repository\LocationRepository;
use App\Repository\PlaceRepository;
use App\Service\ImageService;
use App\Repository\RubricRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExhibitController extends Controller
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly LocationRepository $location_repository,
		private readonly PlaceRepository $place_repository,
		private readonly ImageService $image_service,
		private readonly RubricRepository $rubric_repository
	) {}

	public function overview(Request $request)
	{
		$rubric_id = $request->input('rubric');
		$page = $request->input('page', 0);
		$pageSize = $request->input('pageSize', 10);

		if ($rubric_id) {

			$rubric = $this->rubric_repository->find($rubric_id);
			$selectors = [
				'rubric_id' =>  [
					'$eq' => $rubric_id
				]
			];
		} else {
			$selectors = null;
		}

		$exhibits = $this->exhibit_repository->get_exhibits_paginated($selectors, $page, $pageSize);
		$array = [];
		foreach ($exhibits as $exhibit) {
			$array[] = $this->determinate_overview_page_props($exhibit);
		}
		return Inertia::render('Exhibit/ExhibitOverview', [
			'init_props' => [
				'exhibits' => $array,
			],
			'rubric' => isset($rubric) ? $this->rubric_repository->determinate_rubric_props($rubric) : null
		]);
	}

	private function determinate_overview_page_props(Exhibit $exhibit): array
	{
		$place = $this->place_repository->get($exhibit->get_place_id());
		$location = $this->location_repository->get($place->get_location_id());
		
		$tile_props = [
			'id' => $exhibit->get_id(),
			'name' => $exhibit->get_name(),
			'inventory_number' => $exhibit->get_inventory_number(),
			'year_of_manufacture' => $exhibit->get_year_of_manufacture(),
			'manufacturer' => $exhibit->get_manufacturer(),
			'location_name' => $location->get_name(),
			'place_name' => $place->get_name(),
		];
		if ($title_image = $this->image_service->get_internal_title_image($exhibit)) {
			$tile_props ['title_image'] = [
				'id' => $title_image->get_id(),
				'description' => $title_image->get_description(),
				'thumbnail_width' => $title_image->get_thumbnail_width(),
				'thumbnail_height' => $title_image->get_thumbnail_height(),
			];	
		}
		return $tile_props;
	}

	public function details(int $id)
	{
		$exhibit = $this->exhibit_repository->get($id);
		$rubric = $this->rubric_repository->get($exhibit->get_rubric_id());
		$form = $this->create_form($exhibit, true);
		return Inertia::render('Exhibit/Exhibit', [
			'id' => $exhibit->get_id(),
			'name' => $exhibit->get_name(),
			'init_props' => $form,
			'rubric' => $this->rubric_repository->determinate_rubric_props($rubric)
		]);
	}

	public function new()
	{
		$form = $this->create_form(null, false);
		return Inertia::render('Exhibit/Exhibit', [
			'name' => 'Neues Exponat',
			'init_props' => $form
		]);
	}

	public function create(Request $request)
	{
		$inventory_number = $request->input('inventory_number');
		$name = $request->input('name');
		$manufacturer = $request->input('manufacturer');
		$exhibit = new Exhibit(
			inventory_number: $inventory_number,
			name: $name,
			manufacturer: $manufacturer,
			year_of_manufacture: 9999, // TODO Baujahr im Frontend implementieren
			place_id: '0', // TODO Platzangabe im Frontend implementieren
		);
		$this->exhibit_repository->insert($exhibit);
		// sleep(5); // TODO entfernen
		return redirect()->intended(route('exhibit.details', [$exhibit->get_id()], absolute: false));
	}

	private function create_form(?Exhibit $exhibit, ?bool $persisted): array
	{
		if (!$exhibit) {
			$persisted = false; // TODO remove
		}
		$free_texts = $exhibit?->get_free_texts() ?? [];
		$free_text_forms = array_map(static fn(FreeText $free_text): array => [
			'id' => $free_text->get_id(),
			'errs' => [],
			'val' => [
				'heading' => [
					'val' => $free_text->get_heading(),
					'errs' => [],
				],
				'html' => [
					'val' => $free_text->get_html(),
					'errs' => [],
				],
				'is_public' => [
					'val' => $free_text->get_is_public(),
					'errs' => [],
				]
			]
		], $free_texts);

		$exhibit_form = [
			'val' => [
				'inventory_number' => [
					'val' => $exhibit?->get_inventory_number(),
					'errs' => [],
				],
				'manufacturer' => [
					'val' => $exhibit?->get_manufacturer(),
					'errs' => [],
				],
				'name' => [
					'val' => $exhibit?->get_name(),
					'errs' => [],
				],
				'free_texts' => [
					'val' => $free_text_forms,
					'errs' => [],
				],
			],
			'errs' => [],
		];
		if ($exhibit) {
			$exhibit_id = $exhibit->get_id();
			$exhibit_form['id'] = $exhibit_id;
			if ($title_image = $this->image_service->get_internal_title_image($exhibit)) {
				$exhibit_form['title_image'] = [
					'id' => $title_image->get_id(),
					'description' => $title_image->get_description(),
					'image_width' => $title_image->get_image_width(),
					'image_height' => $title_image->get_image_height(),
				];
			}
		}
		return $exhibit_form;
	}
}
