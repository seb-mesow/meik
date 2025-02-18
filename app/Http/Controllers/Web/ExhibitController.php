<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Enum\Category;
use App\Models\Enum\Currency;
use App\Models\Enum\KindOfAcquistion;
use App\Models\Enum\KindOfProperty;
use App\Models\Enum\Language;
use App\Models\Enum\PreservationState;
use App\Models\Exhibit;
use App\Models\Location;
use App\Models\Parts\AcquisitionInfo;
use App\Models\Parts\FreeText;
use App\Models\Parts\Price;
use App\Models\Rubric;
use App\Repository\ExhibitRepository;
use App\Repository\LocationRepository;
use App\Repository\PlaceRepository;
use App\Service\ExhibitService;
use App\Service\ImageService;
use App\Repository\RubricRepository;
use App\Util\DateTimeUtil;
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
 * ohne rubric_id
 * @phpstan-type ExhibitProps array{
 *     id: number,
 *     inventory_number: string,
 *     name: string,
 *     short_description: string,
 *     manufacturer: string,
 *     manufacture_date: string,
 *     preservation_state_id: string,
 *     original_price: array{
 *         amount: number,
 *         currency_id: string,
 *     },
 *     current_value: number,
 *     acquistion_info: array{
 *         date: string,
 *         source: string,
 *         kind_id: string,
 *         purchasing_price: number,
 *     },
 *     kind_of_property_id: string,
 *     device_info?: array{
 *         manufactured_from_date: string,
 *         manufactured_to_date: string,
 *     },
 *     book_info?: array{
 *         authors: string,
 *         language_id: string,
 *         isbn: string,
 *     },
 *     location_id: string,
 *     place_id: string,
 *     connected_exhibit_ids: number[],
 *     free_texts: FreeTextProps[],
 *     title_image?: array{
 *         id: string,
 *         description: string,
 *         image_width: int,
 *         image_height: int,
 *     },
 * }
 * 
 * @phpstan-type IRubrics array{
 *     id: string,
 *     name: string,
 * }
 * @phpstan-type ICategoryWithRubrics array{
 *     id: string,
 *     name: string,
 *     rubrics: IRubrics[],
 * }
 * @phpstan-type ICurrency array{
 *     id: string,
 *     name: string,
 * }
 * @phpstan-type IKindOfAcquistion array{
 *     id: string,
 *     name: string,
 * }
 * @phpstan-type IKindOfProperty array{
 *     id: string,
 *     name: string,
 * }
 * @phpstan-type ILanguage array{
 *     id: string,
 *     name: string,
 * }
 * @phpstan-type IPreservationState array{
 *     id: string,
 *     name: string,
 * }
 * @phpstan-type ILocation array{
 *     id: string,
 *     name: string,
 * }
 * 
 * @phpstan-type SelectableValues array{
 *     categories_with_rubrics: ICategoryWithRubrics[],
 *     location: ILocation[],
 *     preservation_state: IPreservationState[],
 *     kind_of_property: IKindOfProperty[],
 *     kind_of_acquistion: IKindOfAcquistion[],
 *     currency: ICurrency[],
 *     language: ILanguage[],
 * }
 */
class ExhibitController extends Controller
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly LocationRepository $location_repository,
		private readonly PlaceRepository $place_repository,
		private readonly ImageService $image_service,
		private readonly RubricRepository $rubric_repository,
		private readonly ExhibitService $exhibit_service,
		private readonly DateTimeUtil $date_time_util,
	) {}

	public function overview(Request $request): InertiaResponse
	{
		$rubric_id = (string) $request->query('rubric_id', "");
		
		if ($rubric_id === "") {
			$selectors = [];
		} else {
			$selectors = [
				'rubric_id' =>  [
					'$eq' => $rubric_id
				]
			];
		}
		
		$exhibit_tiles = $this->exhibit_service->determinate_tiles_props(page_number: 0, selectors: $selectors);
		
		return Inertia::render('Exhibit/ExhibitOverview', [
			'exhibit_tiles' => $exhibit_tiles,
			'count_per_page' => ExhibitService::DEFAULT_COUNT_PER_PAGE,
		]);
	}

	public function details(int $exhibit_id): InertiaResponse
	{
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$form = $this->create_form($exhibit);
		
		$rubric = $this->rubric_repository->get($exhibit->get_rubric_id());
		$category = $rubric->get_category();
		
		$selectable_values = $this->determinate_selectable_values();
		
		return Inertia::render('Exhibit/Exhibit', [
			'selectable_values' => $selectable_values,
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

	public function new(Request $request): InertiaResponse
	{
		$rubric_id = (string) $request->query('rubric_id', '');
		
		$selectable_values = $this->determinate_selectable_values();
		$props = [
			'selectable_values' => $selectable_values,
		];
		
		if ($rubric_id !== '') {
			$rubric = $this->rubric_repository->get($rubric_id);
			$category = $rubric->get_category();
			$props['category'] = [
				'id' => $category->value,
				'name' => $category->get_pretty_name(),
			];
			$props['rubric'] = [
				'id' => $rubric->get_id(),
				'name' => $rubric->get_name(),
			];
		}
		
		return Inertia::render('Exhibit/Exhibit', $props);
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
		$acquistion_info = $exhibit->get_acquistion_info();
		$original_price = $exhibit->get_original_price();
		
		$exhibit_form = [
			'id' => $exhibit->get_id(),
			
			// Kerndaten
			'inventory_number' => $exhibit->get_inventory_number(),
			'name' => $exhibit->get_name(),
			'short_description' => $exhibit->get_short_description(),
			'location_id' => $place->get_location_id(),
			'place_id' => $place->get_id(),
			// TODO connected_exhibits
			
			// Bestandsdaten
			'preservation_state_id' => $exhibit->get_preservation_state()->value,
			'current_value' => $exhibit->get_current_value(),
			'kind_of_property_id' => $exhibit->get_kind_of_property()->value,
			
			// Zugangsdaten
			'acquistion_info' => [
				'date' => $this->date_time_util->format_as_iso_date($acquistion_info->get_date()),
				'source'=> $acquistion_info->get_source(),
				'kind_id' => $acquistion_info->get_kind()->value,
				'purchasing_price' => $acquistion_info->get_purchasing_price(),
			],
			
			// Geräte- und Buchinformationen
			'manufacturer' => $exhibit->get_manufacturer(),
			'manufacture_date' => $exhibit->get_manufacture_date(),
			'original_price' => [
				'amount' => $original_price->get_amount(),
				'currency_id' => $original_price->get_currency()->value,
			],
			
			// Freitexte
			'free_texts' => $free_text_forms,
		];
		
		if ($exhibit->is_device()) {
			$device_info = $exhibit->get_device_info();
			$exhibit_form['device_info'] = [
				'manufactured_from_date' => $device_info->get_manufactured_from_date(),
				'manufactured_to_date' => $device_info->get_manufactured_to_date(),
			];
		} else {
			$book_info = $exhibit->get_book_info();
			$exhibit_form['book_info'] = [
				'authors' => $book_info->get_authors(),
				'language_id' => $book_info->get_language()->value,
				'isbn' => $book_info->get_isbn(),
			];
		}
		
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
	
	/**
	 * @return SelectableValues
	 */
	private function determinate_selectable_values(): array {
		$_this = $this;
		$all_categories_with_rubrics = Category::cases();
		$all_categories_with_rubrics = array_map(static function (Category $category) use ($_this): array {
			// TODO optimierbar
			$all_rubrics = $_this->rubric_repository->get_all_by_category_id($category->get_id());
			return [
				'id' => $category->get_id(),
				'name' => $category->get_pretty_name(),
				'rubrics' => array_map(static fn(Rubric $rubric): array => [
					'id' => $rubric->get_id(),
					'name' => $rubric->get_name(),
				], $all_rubrics),
			];
		}, $all_categories_with_rubrics);
		
		$all_locations = $this->location_repository->get_all();
		$all_locations = array_map(static fn(Location $location): array => [
			'id' => $location->get_id(),
			'name' => $location->get_name(),
		], $all_locations);
		
		$all_preservation_states = PreservationState::cases();
		$all_preservation_states = array_map(static fn(PreservationState $state): array => [
			'id' => $state->value,
			'name' => $state->get_name(),
		], $all_preservation_states);
		
		$all_kinds_of_property = KindOfProperty::cases();
		$all_kinds_of_property = array_map(static fn(KindOfProperty $kind): array => [
			'id' => $kind->value,
			'name' => $kind->get_name(),
		], $all_kinds_of_property);
		
		$all_kinds_of_acquistion = KindOfAcquistion::cases();
		$all_kinds_of_acquistion = array_map(static fn(KindOfAcquistion $kind): array => [
			'id' => $kind->value,
			'name' => $kind->get_name(),
		], $all_kinds_of_acquistion);
		
		$all_currencies = Currency::cases();
		$all_currencies = array_map(static fn(Currency $currency): array => [
			'id' => $currency->value,
			'name' => $currency->get_name(),
		], $all_currencies);
		
		$all_languages = Language::cases();
		$all_languages = array_map(static fn(Language $language): array => [
			'id' => $language->value,
			'name' => $language->get_name(),
		], $all_languages);
		
		return [
			'categories_with_rubrics' => $all_categories_with_rubrics,
			'location' => $all_locations,
			'preservation_state' => $all_preservation_states,
			'kind_of_property' => $all_kinds_of_property,
			'kind_of_acquistion' => $all_kinds_of_acquistion,
			'currency' => $all_currencies,
			'language' => $all_languages,
		];
	}
}
