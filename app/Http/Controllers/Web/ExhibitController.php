<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Models\FreeText;
use App\Repository\ExhibitRepository;
use App\Util\FormTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use JMS\Serializer\Serializer;

class ExhibitController extends Controller
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly Serializer $serializer,
		private readonly FormTransformer $form_transformer,
	) {}

	public function overview()
	{
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

	public function details(string $id)
	{
		$exhibit = $this->exhibit_repository->get($id);
		$form = $this->create_form($exhibit, true);
		return Inertia::render('Exhibit/Exhibit', [
			'id' => $exhibit->get_id(),
			'name' => $exhibit->get_name(),
 			'form' => $form
		]);
	}
	
	public function new()
	{
		$form = $this->create_form(null, false);
		return Inertia::render('Exhibit/Exhibit', [
			'form' => $form
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
		);
		$exhibit = $this->exhibit_repository->insert($exhibit);
		sleep(5); // TODO entfernen
		return redirect()->intended(route('exhibit.details', [$exhibit->get_id()], absolute: false));
	}
	
	private function create_form(?Exhibit $exhibit, bool $persisted): array {
		$free_texts = [];
		foreach ($exhibit?->get_free_texts() ?? [] as $index => $free_text) {
			$free_texts[$index] = [
				'heading' => $free_text->get_heading(),
				'html' => $free_text->get_html(),
				'is_public' => $free_text->get_is_public()
			];
		}
		
		return $this->form_transformer->create_form(
			id: 'exhibit',
			val: [
				'inventory_number' => $exhibit?->get_inventory_number(),
				'manufacturer' => $exhibit?->get_manufacturer(),
				'name' => $exhibit?->get_name(),
				'free_texts' => $free_texts,
			],
			persisted: $persisted
		);
	}
}
