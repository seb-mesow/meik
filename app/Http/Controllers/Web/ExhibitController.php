<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Enum\Currency;
use App\Models\Enum\KindOfAcquistion;
use App\Models\Enum\KindOfProperty;
use App\Models\Enum\PreservationState;
use App\Models\Exhibit;
use App\Models\Location;
use App\Models\Parts\AcquisitionInfo;
use App\Models\Parts\FreeText;
use App\Models\Parts\Price;
use App\Repository\ExhibitRepository;
use App\Repository\LocationRepository;
use App\Repository\PlaceRepository;
use App\Service\ExhibitService;
use App\Service\ImageService;
use App\Repository\RubricRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * @phpstan-type FreeTextProps array{
 *     id: number,
 *     heading: string,
 *     html: string,
 *     is_public: bool,
 * }
 * 
 * @phpstan-type ExhibitProps array{
 *     id: number,
 *     name: string,
 *     inventory_number: string,
 *     short_description: string,
 *     location: string,
 *     place: string,
 *     manufacturer: string,
 *     title_image?: array{
 *         id: string,
 *         description: string,
 *         image_width: int,
 *         image_height: int,
 *     },
 *     free_texts: FreeTextProps[],
 * }
 */
class ExhibitController extends Controller
{
	private const int COUNT_PER_PAGE = 20;
	
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
		$rubric_id = (string) $request->query('rubric_id', "");
		
		$breadcrumbs = [];
		$main_props = [
			'count_per_page' => self::COUNT_PER_PAGE, // für ähnliche AJAX-Route
		];
		
		if ($rubric_id === "") {
			$selectors = [];
		} else {
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
		}
		
		$exhibits = $this->exhibit_repository->get_paginated(
			page_number: 0,
			count_per_page: self::COUNT_PER_PAGE,
			additonal_selectors: $selectors,
		);
		$main_props['exhibits'] = $this->exhibit_service->determinate_tiles_props($exhibits);
		
		return Inertia::render('Exhibit/ExhibitOverview', [
			'main' => $main_props,
			'breadcrumb' => $breadcrumbs
		]);
	}

	public function details(int $exhibit_id): InertiaResponse
	{
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$form = $this->create_form($exhibit);
		
		$rubric = $this->rubric_repository->get($exhibit->get_rubric_id());
		$category = $rubric->get_category();
		
		$all_locations = $this->location_repository->get_all();
		$all_locations = array_map(static fn(Location $location): string => $location->get_name(), $all_locations);
		
		return Inertia::render('Exhibit/Exhibit', [
			'all_locations' => $all_locations,
			'exhibit_props' => $form,
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
		// TODO set category and rubric
		return Inertia::render('Exhibit/Exhibit', []);
	}

	public function create(Request $request): RedirectResponse
	{
		$inventory_number = $request->input('inventory_number');
		$name = $request->input('name');
		$short_description = $request->input('short_description');
		$manufacturer = $request->input('manufacturer');
		
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
		// sleep(5); // TODO entfernen
		return redirect()->intended(route('exhibit.details', [$exhibit->get_id()], absolute: false));
	}

	/**
	 * @return ExhibitProps
	 */
	private function create_form(Exhibit $exhibit): array
	{
		$free_texts = $exhibit?->get_free_texts() ?? [];
		$free_text_forms = array_map(static fn(FreeText $free_text): array =>
			self::create_free_text_form($free_text), $free_texts);
		
		$place = $this->place_repository->get($exhibit->get_place_id());
		$location = $this->location_repository->get($place->get_location_id());
		
		$exhibit_form = [
			'id' => $exhibit->get_id(),
			'inventory_number' => $exhibit->get_inventory_number(),
			'name' => $exhibit->get_name(),
			'short_description' => $exhibit->get_short_description(),
			'location' => $location->get_name(),
			'place' => $place->get_name(),
			'manufacturer' => $exhibit->get_manufacturer(),
			'free_texts' => $free_text_forms,
		];
		
		if ($title_image = $this->image_service->get_internal_title_image($exhibit)) {
			$exhibit_form['title_image'] = [
				'id' => $title_image->get_id(),
				'description' => $title_image->get_description(),
				'image_width' => $title_image->get_image_width(),
				'image_height' => $title_image->get_image_height(),
			];
		}
		
		return $exhibit_form;
	}
	
	/**
	 * @return FreeTextProps
	 */
	private static function create_free_text_form(FreeText $free_text): array {
		return [
			'id' => $free_text->get_id(),
			'heading' => $free_text->get_heading(),
			'html' => $free_text->get_html(),
			'is_public' => $free_text->get_is_public(),
		];
	}
}
