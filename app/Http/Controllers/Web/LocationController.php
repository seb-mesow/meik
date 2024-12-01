<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Repository\LocationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use stdClass;

class LocationController extends Controller
{
    private Serializer $serializer;

    public function __construct(
        private readonly LocationRepository $location_repository
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function overview()
    {
        $locations = $this->location_repository->get_locations_paginated();
        $array = array_map(fn($location) => $this->location_repository->objectFromLocation($location), $locations);
        $count = $this->location_repository->get_locations_count();
        return Inertia::render('Locations/Locations', [
            'locations' => $array,
            'count' => $count
        ]);
    }
}
