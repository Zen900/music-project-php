<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Clock\DatePoint;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $albumTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $albumCover = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info = null;

    #[ORM\Column(type: 'day_point')]
    private ?DatePoint $releaseDate = null;

    /**
     * @var Collection<int, Song>
     */
    #[ORM\OneToMany(targetEntity: Song::class, mappedBy: 'album', orphanRemoval: true)]
    private Collection $songs;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlbumTitle(): ?string
    {
        return $this->albumTitle;
    }

    public function setAlbumTitle(string $albumTitle): static
    {
        $this->albumTitle = $albumTitle;

        return $this;
    }

    public function getAlbumCover(): ?string
    {
        return $this->albumCover;
    }

    public function setAlbumCover(?string $albumCover): static
    {
        $this->albumCover = $albumCover;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): static
    {
        $this->info = $info;

        return $this;
    }

    public function getReleaseDate(): ?DatePoint
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(DatePoint $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return Collection<int, Song>
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    public function addSong(Song $song): static
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
            $song->setAlbum($this);
        }

        return $this;
    }

    public function removeSong(Song $song): static
    {
        if ($this->songs->removeElement($song)) {
            // set the owning side to null (unless already changed)
            if ($song->getAlbum() === $this) {
                $song->setAlbum(null);
            }
        }

        return $this;
    }
}
