<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\ExhibitRepository;

class ExhibitApiController extends Controller
{

    public function __construct(private readonly ExhibitRepository $exhibit_repository) {
    }

    public function get_all_exhibits() {
        return $this->exhibit_repository->get_all_exhibits();
    }

    public function get_exhibit(string $id) {

        return $this->exhibit_repository->get_exhibit($id);
    }
}
