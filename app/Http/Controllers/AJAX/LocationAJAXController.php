<?php

declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Repository\LocationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class LocationAJAXController extends Controller
{
    private Serializer $serializer;

    public function __construct(
        private readonly LocationRepository $location_repository
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function get_locations_paginated(Request $request)
    {
        $page = (int)$request->input('page', 0);
        $pageSize = (int)$request->input('pageSize', 10);
        $locations = $this->location_repository->get_locations_paginated($page, $pageSize);
        $array = array_map(fn($location) => $this->location_repository->objectFromLocation($location), $locations);
        return $this->serializer->serialize($array, 'json');
    }

    public function post_location(Request $request)
    {
        $location = $this->serializer->deserialize($request->getContent(), Location::class, 'json');
        return $this->serializer->serialize($this->location_repository->create($location), 'json');
    }

    public function put_location(Request $request)
    {
        $location = $this->serializer->deserialize($request->getContent(), Location::class, 'json');
        return $this->serializer->serialize($this->location_repository->update($location), 'json');
    }

    public function delete_location(string $id)
    {
        $this->location_repository->delete($id);
        return new Response('', 204);
    }
}
