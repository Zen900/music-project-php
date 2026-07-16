<?php

namespace App\Service;

use App\Entity\Album;
use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SongService
{
    public function __construct(private EntityManagerInterface $entityManager, private string $songsDirectory) {
    }

    public function uploadSong(Song $song, Album $album, ?UploadedFile $audioFile): bool
    {
        if (!$audioFile) {
            return false;
        }

        try {
            $fileName = uniqid() . '.' . $audioFile->guessExtension();

            $audioFile->move(
                $this->songsDirectory,
                $fileName
            );

            $song->setAudioFile($fileName);


            $getID3 = new \getID3();

            $fileInfo = $getID3->analyze(
                $this->songsDirectory . '/' . $fileName
            );

            $duration = (int) $fileInfo['playtime_seconds'];

            $song->setLength($duration);
            $song->setAlbum($album);


            $this->entityManager->persist($song);
            $this->entityManager->flush();

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

}