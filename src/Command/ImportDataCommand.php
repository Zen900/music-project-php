<?php

namespace App\Command;

use App\Repository\AlbumRepository;
use App\Entity\Album;
use App\Entity\Song;
use App\Entity\Lyrics;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface; 

#[AsCommand(
    name: 'app:import-jamendo',
    description: 'Import albums and songs from Jamendo API',
)]
class ImportDataCommand extends Command
{
    private string $jsonPath;
    private string $albumsUrl;

    public function __construct(private HttpClientInterface $client, private EntityManagerInterface $entityManager, 
                                private AlbumRepository $albumRepository, string $projectDir, private string $clientId,
                                private int $artistId,)
    {
        parent::__construct();
        $this->jsonPath = $projectDir . '/var/albums.json';
        $this->albumsUrl = "https://api.jamendo.com/v3.0/albums/?client_id=" . $this-> clientId . "&artist_id=" .
                $this->artistId . "&offset=20&format=json";
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Importing data...');

        if ($this->albumRepository->count([]) > 0) 
        {
            $output->writeln('Albums already exist.');
            return Command::SUCCESS;
        }

        $json = $this->getData($output);

        if ($json == null) {
            $output->writeln('Could not load data');
            return Command::FAILURE;
        }

        $data = json_decode($json, true);

        $this->fillDatabase($json);

        $output->writeln('Import finished.');

        return Command::SUCCESS;
    }

    public function fetchData(string $url): array
    {
        $response = $this->client->request('GET',$url);
        return $response->toArray();
    }

    public function downloadSeederData() 
    {
        $albums = $this->fetchData($this->albumsUrl);
        foreach ($albums['results'] as $i => $album) {
            $tracksUrl = "https://api.jamendo.com/v3.0/tracks/?client_id=" . $this-> clientId .
                        "&include[]=lyrics&include[]=musicinfo&limit=200&album_id=" . $album['id'] . "&format=json";

            $songs = $this->fetchData($tracksUrl);
                dump(
        $album['name'],
        count($songs['results'])
    );
            $albums['results'][$i]['songs'] = $songs['results'];
        }
        return json_encode($albums, JSON_PRETTY_PRINT);
    }

    private function saveToFile(string $json): void
    {
        file_put_contents($this->jsonPath, $json);
    }

    private function readAlbumsJson(): string
    {
        $json = file_get_contents($this->jsonPath);
        return $json;
    }

    private function getData()
    {
        if (file_exists($this->jsonPath)) {
            try {
                return $this->readAlbumsJson();
            } catch (\Exception $e) {
                return null;
            }
        }
        $data;
        try {
            $data = $this->downloadSeederData();
        } catch (\Exception $e) {
            return null;
        }

        try {
            $this->saveToFile($data);
        } catch (\Exception $e) {
            return null;
        }

        return $data;
    }

    public function fillDatabase (String $json) 
    {
        $albums = json_decode($json, true);

        foreach ($albums['results'] as $albumJson) 
        {
            $album = new Album();
            $album->setAlbumTitle($albumJson['name']);
            $album->setAlbumCover($albumJson['name'] . ".jpeg");
            $album->setInfo($albumJson['artist_name']);
            $album->setReleaseDate(new \DateTime ($albumJson['releasedate']));

            $this->entityManager->persist($album);

            foreach ($albumJson['songs'] as $songJson){
                $musicInfoJson;
                try {
                    $musicInfoJson = json_encode($songJson['musicinfo']['tags']['genres'] ?? []);
                } catch (\Exception $e) {
                    return;
                }

                $song = new Song();
                $song->setSongTitle($songJson['name']);
                $song->setAudioFile($songJson['name'] . ".mp3");
                $song->setLength($songJson['duration']);
                $song->setInfo($musicInfoJson);
                $song->setAlbum($album);

                $this->entityManager->persist($song);

                if (!empty($songJson['lyrics']))
                {
                    $lyricsOfSong = explode("\n", $songJson['lyrics']);

                    $songDuration = $songJson['duration'];
                    $lineCount = count($lyricsOfSong);
                    $secondsPerLine = (float) $songDuration / $lineCount;
                    $currentTime = 0;

                    foreach ($lyricsOfSong as $lyricsLine)
                    {
                        $lyrics = new Lyrics();
                        $lyrics->setLine($lyricsLine);
                        $lyrics->setTime((int) round($currentTime));
                        $lyrics->setSong($song);

                        $this->entityManager->persist($lyrics);

                        $currentTime += $secondsPerLine;
                    }
                }
            }
        }

        $this->entityManager->flush();

    }
}