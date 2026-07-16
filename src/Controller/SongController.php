<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\Album;
use App\Repository\SongRepository;
use App\Form\Type\SongType;
use App\Service\SongService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SongController extends AbstractController
{
    #[Route('/song/{id}', name: 'song_show')]
    public function showSingleSong(Song $song): Response
    {

        return $this->render('song.html.twig', [
            'song' => $song,
        ]);
    }

    #[Route('/album/{id}/upload-song', name: 'upload_song')]
    public function uploadSong(Request $request, Album $album, SongService $songService): Response
    {
        $song = new Song();

        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $audioFile = $form->get('audioFile')->getData();


            if ($songService->uploadSong($song, $album, $audioFile)) {

                $this->addFlash(
                    'success',
                    'Song successfully uploaded!'
                );

            } else {

                $this->addFlash(
                    'error',
                    'Uploading the song failed.'
                );
            }

            return $this->redirectToRoute(
                'album_show',
                ['id' => $album->getId()]
            );
        }

        return $this->render('uploadSong.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}