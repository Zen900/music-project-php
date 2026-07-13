<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\Album;
use App\Repository\SongRepository;
use App\Form\Type\SongType;
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
    public function uploadSong(Request $request, EntityManagerInterface $entityManager, Album $album,): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $audioFile = $form->get('audioFile')->getData();

            if($audioFile) {
                try {
                    $fileName = uniqid() . '.' . $audioFile->guessExtension();
                    $audioFile->move(
                        $this->getParameter('songs_directory'),
                        $fileName
                    );
                    $song->setAudioFile($fileName);
                    $getID3 = new \getID3;

                    $fileInfo = $getID3->analyze(
                        $this->getParameter('songs_directory') . '/' . $fileName
                    );

                    $duration = (int)$fileInfo['playtime_seconds'];
                    $song->setLength($duration);
                    $song->setAlbum($album);
                    $entityManager->persist($song);
                    $entityManager->flush();

                    $this->addFlash(
                        'success',
                        'Song successfully uploaded!'
                    );
                }
                catch (\Exception $e) {
                    $this->addFlash(
                        'error',
                        'Uploading the song failed.'
                    );
                }

                return $this->redirectToRoute('album_show', ['id' => $album->getId()]);
            }
            else {
                $this->addFlash(
                    'error',
                    'Please select an audio file.'
                );
            }
        }
        
        return $this->render('uploadSong.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}