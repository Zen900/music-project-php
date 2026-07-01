<?php

namespace App\Controller;

use App\Entity\Album;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AlbumController extends AbstractController
{
    #[Route('/album/{id}', name: 'album_show')]
    public function show(Album $album): Response
    {

        return $this->render('albums.html.twig', [
            'album' => $album,
        ]);
    }
}