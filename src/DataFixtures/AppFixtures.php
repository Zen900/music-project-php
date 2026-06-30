<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Song;
use App\Entity\Lyrics;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture 
{
    public function load(ObjectManager $manager): void
    {
        $album = new Album();
        $album->setAlbumTitle("Album A");
        $album->setAlbumCover("albumA.jpg");
        $album->setInfo("Artist A");
        $album->setReleaseDate(new \DateTime('2025-10-20'));

        $song = new Song();
        $song->setSongTitle("Song A");
        $song->setAudioFile("songA.mp3");
        $song->setLength(240);
        $song->setInfo("some info");
        $song->setAlbum($album);

        $lyric1 = new Lyric();
        $lyric1->setLine("Start of the song");
        $lyric1->setTime(1);
        $lyric1->setSong($song);

        $lyric2 = new Lyric();
        $lyric2->setLine("This is the second line");
        $lyric2->setTime(5);
        $lyric2->setSong($song);

        $manager->persist($album);
        $manager->persist($song);
        $manager->persist($lyric1);
        $manager->persist($lyric2);
        $manager->flush();
    }
}

