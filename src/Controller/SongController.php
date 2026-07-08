<?php

namespace App\Controller;

use App\Entity\Song;
use App\Repository\SongRepository;
use App\Form\Type\SongType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/uploadsong', name: 'upload_song')]
    public function uploadSong(Request $request, EntityManagerInterface $entityManager): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $audioFile = $form->get('audioFile')->getData();

            if($audioFile) {
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
            }

            $entityManager->persist($song);
            $entityManager->flush();

            return $this->redirectToRoute('homepage');
        }
        return $this->render('uploadSong.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}