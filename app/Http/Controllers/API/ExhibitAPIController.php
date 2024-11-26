<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Repository\ExhibitRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use stdClass;

class ExhibitAPIController extends Controller
{
	private Serializer $serializer;

    public function __construct(
        private readonly ExhibitRepository $exhibit_repository
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
	 * TODO als API-Route umformulieren
	 */ 
    public function get_all_exhibits()
    {
        $exhibits = $this->exhibit_repository->get_all();
        $array = array_map(fn($exhibit) => $this->exhibit_repository->create_doc_from_exhibit($exhibit), $exhibits);
 
        return Inertia::render('Exhibits/Exhibits', [
            'exhibits' => $array
        ]);
    }

	/**
	 * TODO als API-Route umformulieren
	 */ 
    public function get_exhibit(string $id)
    {
        return Inertia::render('Exhibits/Exhibit', [
            'exhibit' => $this->exhibit_repository->find($id)
        ]);
    }
}
