<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Repository\ExhibitRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use JMS\Serializer\Serializer;

class ExhibitController extends Controller
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly Serializer $serializer
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
		return Inertia::render('Exhibits/ExhibitOverview', [
			'exhibits' => $array
		]);
	}

	public function details(string $id)
	{
		$exhibit = $this->exhibit_repository->get($id);
		return Inertia::render('Exhibits/Exhibit', [
			'id' => $exhibit->get_id(),
			'form' => $this->create_form_from_exhibit($exhibit)
		]);
	}
	
	public function new()
	{
		return Inertia::render('Exhibits/Exhibit', [
			'form' => $this->create_form_from_exhibit()
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
	
	private function create_form_from_exhibit(?Exhibit $exhibit = null): array {
		return [
			'vals' => [
				'inventory_number' => [
					'id' => 'inventory_number',
					'val' => $exhibit?->get_inventory_number(),
					'errs' => []
				],
				'manufacturer' => [
					'id' => 'manufacturer',
					'val' => $exhibit?->get_manufacturer(),
					'errs' => []
				],
				'name' => [
					'id' => 'name',
					'val' => $exhibit?->get_name(),
					'errs' => []
				],
			],
			'errs' => []
		];
	}
}
