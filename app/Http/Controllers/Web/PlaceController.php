<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Repository\PlaceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use stdClass;

class PlaceController extends Controller
{
    private Serializer $serializer;

    public function __construct(
        private readonly PlaceRepository $place_repository
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function get_all_places()
    {
        $places = $this->place_repository->get_all_places();
        $array = array_map(fn($place) => $this->place_repository->objectFromPlace($place), $places);
 
        return Inertia::render('Places/Places', [
            'places' => $array
        ]);
    }

    public function get_place(string $id)
    {
        return Inertia::render('Places/Place', [
            'place' => $this->place_repository->get_place($id)
        ]);
    }
}
