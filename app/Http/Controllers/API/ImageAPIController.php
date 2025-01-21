<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\ExhibitRepository;
use App\Repository\ImageRepository;

class ImageAPIController extends Controller
{

    public function __construct(
        private readonly ImageRepository $image_repository
    ) {}

    public function get_image(string $id)
    {
        ['content_type' => $content_type, 'file' => $file] = $this->image_repository->get_file($id);
        return response($file)
            ->header('Content-Type', $content_type);
    }

    public function get_thumbnail(string $id)
    {
        ['content_type' => $content_type, 'file' => $file] = $this->image_repository->get_thumbnail($id);
        return response($file)
            ->header('Content-Type', $content_type);
    }
}
