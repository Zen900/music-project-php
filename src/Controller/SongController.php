<?php

namespace App\Controller;

use App\Entity\Song;
use App\Repository\SongRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SongController extends AbstractController
{
    #[Route('/', name: 'song')]
    public function showAllSong(SongRepository $songRepository): Response
    {
        $songs = $songRepository->findAll();

        return $this->render('playerControl.html.twig', [
            'songs' => $songs,
        ]);
    }
}