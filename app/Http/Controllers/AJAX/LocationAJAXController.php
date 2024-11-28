<?php

declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Repository\LocationRepository;
use Illuminate\Http\Request;
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
        return $this->location_repository->delete($id);
    }
}
