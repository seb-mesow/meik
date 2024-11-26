<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Repository\ExhibitRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use stdClass;

class ExhibitController extends Controller
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly Serializer $serializer
	) {}

	public function all_exhibits()
	{
		$exhibits = $this->exhibit_repository->get_all();
		$array = array_map(static function(Exhibit $exhibit): array {
			return [
				'name' => $exhibit->get_name(),
			];
		}, $exhibits);
		return Inertia::render('Exhibits/ExhibitOverview', [
			'exhibits' => $array
		]);
	}

    public function get_exhibit(string $id)
    {
        return Inertia::render('Exhibits/Exhibit', [
            'exhibit' => $this->exhibit_repository->find($id)
        ]);
    }
}