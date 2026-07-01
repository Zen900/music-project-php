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
    public function showSingleAlbum(Album $album): Response
    {

        return $this->render('albums.html.twig', [
            'album' => $album,
        ]);
    }

    #[Route('/', name: 'album_index')]
    public function showAllAlbums(AlbumRepository $albumRepository): Response
    {
        $albums = $albumRepository->findAll();

        return $this->render('homepage.html.twig', [
            'albums' => $albums,
        ]);
    }
}