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
			'form' => $this->create_form_from_exhibit($exhibit)
		]);
	}
	
	public function create(Request $request)
	{
		$exhibit = $this->serializer->deserialize($request->getContent(), Exhibit::class, 'json');
		$exhibit = $this->exhibit_repository->insert($exhibit);
		return redirect()->intended(route('exhibit.details', [$exhibit->get_id()], absolute: false));
	}
	
	private function create_form_from_exhibit(Exhibit $exhibit): array {
		return [
			'vals' => [
				'inventory_number' => [
					'id' => 'inventory_number',
					'val' => $exhibit->get_inventory_number(),
					'errs' => []
				],
				'manufacturer' => [
					'id' => 'manufacturer',
					'val' => $exhibit->get_manufacturer(),
					'errs' => []
				],
				'name' => [
					'id' => 'name',
					'val' => $exhibit->get_name(),
					'errs' => []
				],
			],
			'errs' => []
		];
	}
}
