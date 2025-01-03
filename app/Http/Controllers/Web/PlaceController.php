<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Repository\LocationRepository;
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
        private readonly PlaceRepository $place_repository,
        private readonly LocationRepository $location_repository
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function overview(Request $request)
    {
        $locationId = $request->input('location');
        $places = $this->place_repository->get_places_paginated($locationId);
        $location = $this->location_repository->get_location($locationId);
        $array = array_map(fn($place) => $this->place_repository->objectFromPlace($place), $places);
        $count = $this->place_repository->get_places_count();
        return Inertia::render('Locations/Places/Places', [
            'places' => $array,
            'count' => $count,
            'location_id' => $location->get__id(),
            'location_name' => $location->get_name()
        ]);
    }
}
