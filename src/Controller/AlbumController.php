<?php

namespace App\Controller;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use App\Form\Type\AlbumType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/', name: 'homepage')]
    public function showAllAlbums(AlbumRepository $albumRepository): Response
    {
        $albums = $albumRepository->findAll();

        return $this->render('homepage.html.twig', [
            'albums' => $albums,
        ]);
    }

    #[Route('/upload', name: 'upload_album')]
    public function uploadAlbum(Request $request, EntityManagerInterface $entityManager): Response
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $albumCover = $form->get('albumCover')->getData();
            
            if($albumCover) {
                try{
                    $fileName = uniqid() . '.' . $albumCover->guessExtension();

                    $albumCover->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );

                    $album->setAlbumCover($fileName);
                }
                catch(\Exception $e) {
                    $this->addFlash(
                        'warning',
                        'Album uploaded, but cover upload failed.'
                    );
                }
            }

            $entityManager->persist($album);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Album successfully uploaded!'
            );

            return $this->redirectToRoute('homepage');

        }

        return $this->render('upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}