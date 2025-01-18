<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Models\FreeText;
use App\Repository\ExhibitRepository;
use App\Repository\ImageRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use JMS\Serializer\Serializer;

class ExhibitController extends Controller
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly ImageRepository $image_repository,
	) {}

	public function overview() {
		$exhibits = $this->exhibit_repository->get_all();
		$array = array_map(static function(Exhibit $exhibit): array {
			return [
				'id' => $exhibit->get_id(),
				'name' => $exhibit->get_name(),
			];
		}, $exhibits);
		return Inertia::render('Exhibit/ExhibitOverview', [
			'exhibits' => $array
		]);
	}

	public function details(int $id) {
		$exhibit = $this->exhibit_repository->get($id);
		$form = $this->create_form($exhibit, true);
		return Inertia::render('Exhibit/Exhibit', [
			'id' => $exhibit->get_id(),
			'name' => $exhibit->get_name(),
 			'init_props' => $form
		]);
	}
	
	public function new() {
		$form = $this->create_form(null, false);
		return Inertia::render('Exhibit/Exhibit', [
			'name' => 'Neues Exponat',
			'init_props' => $form
		]);
	}
	
	public function create(Request $request) {
		$inventory_number = $request->input('inventory_number');
		$name = $request->input('name');
		$manufacturer = $request->input('manufacturer');
		$exhibit = new Exhibit(
			inventory_number: $inventory_number,
			name: $name,
			manufacturer: $manufacturer,
		);
		$this->exhibit_repository->insert($exhibit);
		// sleep(5); // TODO entfernen
		return redirect()->intended(route('exhibit.details', [$exhibit->get_id()], absolute: false));
	}
	
	private function create_form(?Exhibit $exhibit, ?bool $persisted): array {
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
			$exhibit_form['title_image_id'] = $this->image_repository->get_title_image_id($exhibit_id);
		}
		return $exhibit_form;
	}
}
