<?php

namespace App\Http\Controllers;

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
    private Serializer $serializer;

    public function __construct(
        private readonly ExhibitRepository $exhibit_repository
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function get_all_exhibits()
    {
        return Inertia::render('Exhibits/Exhibits', [
            'exhibits' => $this->exhibit_repository->get_all_exhibits()
        ]);
    }

    public function get_exhibit(string $id)
    {
        return Inertia::render('Exhibits/Exhibit', [
            'exhibit' => $this->exhibit_repository->get_exhibit($id)
        ]);
    }

    public function post_exhibit(Request $request)
    {
        $exhibit = $this->serializer->deserialize($request->getContent(), Exhibit::class, 'json');

        return $this->exhibit_repository->create($exhibit);
    }

    public function put_exhibit(Request $request)
    {
        $exhibit = $this->serializer->deserialize($request->getContent(), Exhibit::class, 'json');

        return $this->exhibit_repository->update($exhibit);
    }

    public function delete_exhibit(string $id)
    {
        return $this->exhibit_repository->delete($id);
    }
}
