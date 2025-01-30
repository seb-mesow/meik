<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Models\Parts\FreeText;
use App\Repository\ExhibitRepository;
use App\Repository\LocationRepository;
use App\Repository\PlaceRepository;
use App\Service\ExhibitService;
use App\Service\ImageService;
use App\Repository\RubricRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ExhibitController extends Controller
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly LocationRepository $location_repository,
		private readonly PlaceRepository $place_repository,
		private readonly ImageService $image_service,
		private readonly RubricRepository $rubric_repository,
		private readonly ExhibitService $exhibit_service,
	) {}

	public function overview(Request $request): InertiaResponse
	{
		$rubric_id = $request->input('rubric');
		$page = $request->input('page', 0);
		$pageSize = $request->input('pageSize', 50);
		
		$breadcrumbs = [];
		$main_props = [];
		
		if ($rubric_id) {
			$selectors = [
				'rubric_id' =>  [
					'$eq' => $rubric_id
				]
			];
			
			$rubric = $this->rubric_repository->get($rubric_id);
			$category = $rubric->get_category();
			$main_props['rubric'] = [
				'id' => $rubric->get_id(),
			];
			
			$breadcrumbs = [
				'category' => [
					'id' => $category->value,
					'name' => $category->get_pretty_name(),
				],
				'rubric' => [
					'id' => $rubric->get_id(),
					'name' => $rubric->get_name(),
				],
			];
		} else {
			$selectors = null;
		}
		
		$exhibits = $this->exhibit_repository->get_exhibits_paginated($selectors, $page, $pageSize);
		$main_props['exhibits'] = $this->exhibit_service->determinate_tiles_props($exhibits);
		
		return Inertia::render('Exhibit/ExhibitOverview', [
			'main' => $main_props,
			'breadcrumb' => $breadcrumbs
		]);
	}



	public function details(int $exhibit_id): InertiaResponse
	{
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$form = $this->create_form($exhibit, true);
		
		$rubric = $this->rubric_repository->get($exhibit->get_rubric_id());
		$category = $rubric->get_category();
		
		return Inertia::render('Exhibit/Exhibit', [
			'id' => $exhibit->get_id(),
			'name' => $exhibit->get_name(),
			'init_props' => $form,
			'category' => [
				'id' => $category->value,
				'name' => $category->get_pretty_name(),
			],
			'rubric' => [
				'id' => $rubric->get_id(),
				'name' => $rubric->get_name(),
			],
		]);
	}

	public function new(): InertiaResponse
	{
		$form = $this->create_form(null, false);
		return Inertia::render('Exhibit/Exhibit', [
			'name' => 'Neues Exponat',
			'init_props' => $form
		]);
	}

	public function create(Request $request): RedirectResponse
	{
		$inventory_number = $request->input('inventory_number');
		$name = $request->input('name');
		$manufacturer = $request->input('manufacturer');
		$exhibit = new Exhibit(
			inventory_number: $inventory_number,
			name: $name,
			manufacturer: $manufacturer,
			manufacture_date: '9999', // TODO Baujahr im Frontend implementieren
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
