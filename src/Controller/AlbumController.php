<?php

namespace App\Controller;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AlbumController extends AbstractController
{
    #[Route('/album/{id}', name: 'album_show')]
    public function show(AlbumRepository $albumRepository, int $id): Response
    {
        $album = $albumRepository
            ->find($id);

                    if (!$album) {
            return new Response('Album not found', 404);
        }

        // ...
        return new Response('Check out this great product: '.$album->getAlbumTitle());
    }
}