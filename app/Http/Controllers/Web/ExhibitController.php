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
    private Serializer $serializer;

    public function __construct(
        private readonly ExhibitRepository $exhibit_repository
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function get_all_exhibits()
    {
        $exhibits = $this->exhibit_repository->get_all_exhibits();
        $array = array_map(fn($exhibit) => $this->exhibit_repository->objectFromExhibit($exhibit), $exhibits);
 
        return Inertia::render('Exhibits/Exhibits', [
            'exhibits' => $array
        ]);
    }

    public function get_exhibit(string $id)
    {
        return Inertia::render('Exhibits/Exhibit', [
            'exhibit' => $this->exhibit_repository->get_exhibit($id)
        ]);
    }
}
