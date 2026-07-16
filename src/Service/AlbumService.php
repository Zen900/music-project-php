<?php

namespace App\Service;

use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AlbumService 
{
    public function __construct(private EntityManagerInterface $entityManager,private string $imagesDirectory) {
    }

    public function uploadAlbum(Album $album, ?UploadedFile $albumCover): bool
    {
        if ($albumCover) {
            try {
                $fileName = uniqid() . '.' . $albumCover->guessExtension();

                $albumCover->move(
                    $this->imagesDirectory,
                    $fileName
                );

                $album->setAlbumCover($fileName);

            } catch (\Exception $e) {
                return false;
            }
        }

        $this->entityManager->persist($album);
        $this->entityManager->flush();

        return true;
    }


}