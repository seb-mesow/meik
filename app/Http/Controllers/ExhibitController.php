<?php

namespace App\Http\Controllers;

use App\Models\Exhibit;
use App\Repository\ExhibitRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use stdClass;

class ExhibitController extends Controller
{

    public function __construct(private readonly ExhibitRepository $exhibit_repository) {
    }

    public function get_all_exhibits() {
        return Inertia::render('exhibits', [
            'exhibits' => $this->exhibit_repository->get_all_exhibits()
        ]);
    }

    public function get_exhibit(string $id) {
        return Inertia::render('exhibit', [
            'exhibit' => $this->exhibit_repository->get_exhibit($id)
        ]);
    }

    public function post_exhibit(Request $request) {
       
        $body = json_decode($request->getContent());
        
        $exhibit = (new Exhibit())
        ->set_designation($body->designation)
        ->set_manufacturer($body->manufacturer)
        ->set_location($body->location)
        ->set_year_of_construction($body->year_of_construnction)
        ->set_inventory_number(Exhibit::ID_PREFIX .$body->inventory_number)
        ->set_aquiry_date((new Date));
        
        return $this->exhibit_repository->create($exhibit);

    }

    public function put_exhibit(Request $request) {
       
        $body = json_decode($request->getContent());
        
        $exhibit = (new Exhibit())
        ->set_inventory_number(Exhibit::ID_PREFIX .$body->inventory_number) 
        ->set_designation($body->designation)
        ->set_manufacturer($body->manufacturer)
        ->set_location($body->location) 
        ->set_year_of_construction($body->year_of_construnction)
        ->set_aquiry_date($body->aquiry_date);
        
        return $this->exhibit_repository->update($exhibit);

    }

    public function delete_exhibit(string $id) {

        return $this->exhibit_repository->delete($id);
    }

}
