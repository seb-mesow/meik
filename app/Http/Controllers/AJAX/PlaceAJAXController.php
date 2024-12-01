<?php

declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Repository\PlaceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class PlaceAJAXController extends Controller
{
    private Serializer $serializer;

    public function __construct(
        private readonly PlaceRepository $place_repository
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function get_places_paginated(Request $request)
    {
        $locationId = $request->input('location');

        if (!$locationId) {
            return response(null, 400);
        }

        $page = (int)$request->input('page', 0);
        $pageSize = (int)$request->input('pageSize', 10);
        $places = $this->place_repository->get_places_paginated($locationId, $page, $pageSize);
        $array = array_map(fn($place) => $this->place_repository->objectFromPlace($place), $places);
        return $this->serializer->serialize($array, 'json');
    }

    public function post_place(Request $request)
    {
        $place = $this->serializer->deserialize($request->getContent(), Place::class, 'json');
        return $this->serializer->serialize($this->place_repository->create($place), 'json');
    }

    public function put_place(Request $request)
    {
        $place = $this->serializer->deserialize($request->getContent(), Place::class, 'json');
        return $this->serializer->serialize($this->place_repository->update($place), 'json');
    }

    public function delete_place(string $id)
    {
        $this->place_repository->delete($id);
        return new Response('', 204);
    }
}
