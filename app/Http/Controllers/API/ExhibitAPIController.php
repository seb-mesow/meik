<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\Exhibit;
use App\Models\Image;
use App\Http\Controllers\Controller;
use App\Models\Parts\FreeText;
use App\Repository\ExhibitRepository;
use App\Repository\LocationRepository;
use App\Repository\PlaceRepository;
use App\Repository\RubricRepository;
use App\Service\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 * @phpstan-type APIExhibit array{
 *    name: string,
 *    short_description: string,
 *    location: string,
 *    place: string,
 *    category: string,
 *    rubric: string,
 *    manufacturer: string,
 *    manufacture_date: string,
 *    original_price: APIOriginalPrice,
 *    device_info: APIDeviceInfo,
 *    book_info: APIBookInfo,
 *    connected_exhibits: string[],
 *    free_text_fields: APIFreeText[],
 *    images: APIFullImageInfo[]
 * }
 * 
 * @phpstan-type APIOriginalPrice array{
 *     amount: float,
 *     currency: string
 * },
 * 
 * @phpstan-type APIDeviceInfo array{
 *     manufactured_from_date: string,
 *     manufactured_to_date: string,
 * },
 * 
 * @phpstan-type APIBookInfo array{
 *     author: string,
 *     isbn: string,
 *     language: string
 * },
 * 
 * @phpstan-type APIFreeText array{
 *     title: string,
 *     html: string
 * }
 * 
 * @phpstan-type APIFullImageInfo array{
 *     id: string,
 *     description: string,
 *     image: array{
 *         height: int,
 *         width: int,
 *     },
 *     thumbnail: array{
 *         height: int,
 *         width: int,
 *     }
 * }
 * 
 * @phpstan-type SearchItem array{
 *    id: int,
 *    name: string,
 *    title_image: array{
 *        id: string,
 *        thumbnail: array{
 *            height: int,
 *            width: int
 *        }
 *    }
 * }
 */
class ExhibitAPIController extends Controller
{
	private const int DEFAULT_COUNT_PER_PAGE = 25;
	
	private Serializer $serializer;

	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly PlaceRepository $place_repository,
		private readonly LocationRepository $location_repository,
		private readonly RubricRepository $rubric_repository,
		private readonly ImageService $image_service,
	) {
		$this->serializer = SerializerBuilder::create()->build();
	}
	
	// TODO document API
	// TODO define response objects
	public function get_exhibits_paginated(Request $request): JsonResponse
	{
		$page_number = (int) $request->query('page_number', 0);
		$count_per_page = (int) $request->query('count_per_page', (string) self::DEFAULT_COUNT_PER_PAGE);
		
		$exhibits = $this->exhibit_repository->get_paginated($page_number, $count_per_page);
		
		return response()->json(json_decode($this->serializer->serialize($exhibits, 'json', (new SerializationContext))));
	}

	public function get_exhibit_by_id(int $id): JsonResponse
	{
		$exhibit = $this->exhibit_repository->get($id);
		$api_exhibit = $this->create_api_exhibit_from_exhibit($exhibit);
		return response()->json($api_exhibit);
	}

	public function search_exhibits(string $query): JsonResponse
	{
		$queryParts = explode(' ', $query);
		$selectors = [];

		foreach ($queryParts as $queryPart) {
			$selector = [
				'$or' => [
					[
						'manufacturer' => [
							'$regex' => '(?i)' . $queryPart // Regex für manufacturer
						]
					],
					[
						'name' => [
							'$regex' => '(?i)' . $queryPart // Regex für name
						]
					],
					[
						'inventory_number' => [
							'$eq' => $queryPart // Exakte Übereinstimmung für inventory_number
						]
					]
				]
			];
			$selectorParts[] = $selector;
		}

		$selectors = [
			'$and' => $selectorParts
		];

		$exhibits = $this->exhibit_repository->query_by_selectors($selectors);
		$search_item = $this->create_search_items_from_exhibits($exhibits);
		return response()->json($search_item);
	}

	public function find_exhibits_by_filter(Request $request): JsonResponse
	{
		$fields = $request->all('field');
		$operator = $request->input('operator', 'and');

		switch ($operator) {
			case 'and':
				$operator = [
					'$and'
				];
			case 'or':
				$operator = [
					'$or'
				];
		}

		foreach ($fields['field'] as $field) {
			$field_pair = explode(':', $field);

			$selector = [
				$field_pair[0] => [
					'$eq' => $field_pair[1]
				]
			];
			$selectorParts[] = $selector;
		}

		$selectors = [
			'$and' => $selectorParts
		];
		$exhibits = $this->exhibit_repository->query_by_selectors($selectors);

		return response()->json(json_decode($this->serializer->serialize($exhibits, 'json', (new SerializationContext))));
	}
	
	/**
	 * @param Exhibit $exhibit
	 * @return APIExhibit
	 */
	private function create_api_exhibit_from_exhibit(Exhibit $exhibit): array {
		$api_exhibit = [
			'name' => $exhibit->get_name(),
			'short_description' => $exhibit->get_short_description(),
			'manufacturer' => $exhibit->get_manufacturer(),
			'manufacture_date' => $exhibit->get_manufacture_date(),
			'connected_exhibits' => array_map(static fn(int $id): string => (string) $id, $exhibit->get_connected_exhibit_ids()),
		];
		
		$place = $this->place_repository->get($exhibit->get_place_id());
		$api_exhibit['place'] = $place->get_name();
		$location = $this->location_repository->get($place->get_location_id());
		$api_exhibit['location'] = $location->get_name();

		$rubric = $this->rubric_repository->get($exhibit->get_rubric_id());
		$api_exhibit['rubric'] = $rubric->get_name();
		$category = $rubric->get_category();
		$api_exhibit['category'] = $category->get_name();
		
		$original_price = $exhibit->get_original_price();
		$api_exhibit['original_price'] = [
			'amount' => $original_price->get_amount_in_main_unit(),
			'currency' => $original_price->get_currency()->value,
		];
		
		if ($exhibit->is_device()) {
			$device_info = $exhibit->get_device_info();
			$api_exhibit['device_info'] = [
				'manufactured_from_date' => $device_info->get_manufactured_from_date(),
				'manufactured_to_date' => $device_info->get_manufactured_to_date(),
			];
			// $api_exhibit['book_info'] = null
		} else {
			$book_info = $exhibit->get_book_info();
			$api_exhibit['book_info'] = [
				'authors' => $book_info->get_authors(),
				'isbn' => $book_info->get_isbn(),
				'language' => $book_info->get_language()->value,
			];
			// $api_exhibit['device_info'] = null
		}
		
		$public_images = $this->image_service->get_all_public_images($exhibit);
		$api_exhibit['images'] = $this->create_api_full_image_infos_from_images($public_images);
		
		$public_free_texts = array_filter($exhibit->get_free_texts(), static fn(FreeText $free_text): bool => $free_text->get_is_public());
		$public_free_texts = array_values($public_free_texts);
		$api_exhibit['free_text_fields'] = $this->create_api_free_texts_from_free_texts($public_free_texts);
		
		return $api_exhibit;
	}
	
	/**
	 * @param FreeText[] $free_texts
	 * @return APIFreeText[]
	 */
	private function create_api_free_texts_from_free_texts(array $free_texts): array {
		$_this = $this;
		return array_map(static function(FreeText $free_text) use ($_this): array {
			return $_this->create_api_free_text_from_free_text($free_text);
		}, $free_texts);
	}
	
	/**
	 * @return APIFreeText
	 */
	private function create_api_free_text_from_free_text(FreeText $free_text): array {
		assert($free_text->get_is_public());
		return [
			'title' => $free_text->get_heading(),
			'html' => $free_text->get_html(),
		];
	}
	
	/**
	 * @param Image[] $images
	 * @return APIFullImageInfo[]
	 */
	private function create_api_full_image_infos_from_images(array $images): array {
		$_this = $this;
		return array_map(static function(Image $image) use ($_this): array {
			return $_this->create_api_full_image_info_from_image($image);
		}, $images);
	}
	
	/**
	 * @param IMage $image
	 * @return APIFullImageInfo
	 */
	private function create_api_full_image_info_from_image(Image $image): array {
		assert($image->get_is_public());
		return [
			'id' => $image->get_id(),
			'description' => $image->get_description(),
			'image' => [
				'height' => $image->get_image_height(),
				'width' => $image->get_image_width(),
			],
			'thumbnail' => [
				'height' => $image->get_thumbnail_height(),
				'width' => $image->get_thumbnail_width(),
			],
		];
	}
	
	/**
	 * @param Exhibit[] $exhibits
	 * @return SearchItem[]
	 */
	private function create_search_items_from_exhibits(array $exhibits): array {
		$_this = $this;
		return array_map(static function(Exhibit $exhibit) use ($_this): array {
			return $_this->create_search_item_from_exhibit($exhibit);
		}, $exhibits);
	}
	
	/**
	 * @param Exhibit $exhibit
	 * @return SearchItem
	 */
	private function create_search_item_from_exhibit(Exhibit $exhibit): array {
		$search_item = [
			'id' => $exhibit->get_id(),
			'name' => $exhibit->get_name(),
		];
		if ($title_image = $this->image_service->get_public_title_image($exhibit)) {
			$search_item['title_image'] = [
				'id' => $title_image->get_id(),
				'thumbnail' => [
					'height' => $title_image->get_thumbnail_height(),
					'width' => $title_image->get_thumbnail_width(),
				],
			];
		}
		return $search_item;
	}
}
