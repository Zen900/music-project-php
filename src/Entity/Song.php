<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $songTitle = null;

    #[ORM\Column(length: 255)]
    private ?string $audioFile = null;

    #[ORM\Column]
    private ?int $length = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info = null;

    #[ORM\ManyToOne(inversedBy: 'songs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Album $album = null;

    /**
     * @var Collection<int, Lyrics>
     */
    #[ORM\OneToMany(targetEntity: Lyrics::class, mappedBy: 'song', orphanRemoval: true)]
    private Collection $lyrics;

    public function __construct()
    {
        $this->lyrics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSongTitle(): ?string
    {
        return $this->songTitle;
    }

    public function setSongTitle(string $songTitle): static
    {
        $this->songTitle = $songTitle;

        return $this;
    }

    public function getAudioFile(): ?string
    {
        return $this->audioFile;
    }

    public function setAudioFile(string $audioFile): static
    {
        $this->audioFile = $audioFile;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): static
    {
        $this->length = $length;

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

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): static
    {
        $this->album = $album;

        return $this;
    }

    /**
     * @return Collection<int, Lyrics>
     */
    public function getLyrics(): Collection
    {
        return $this->lyrics;
    }

    public function addLyric(Lyrics $lyric): static
    {
        if (!$this->lyrics->contains($lyric)) {
            $this->lyrics->add($lyric);
            $lyric->setSong($this);
        }

        return $this;
    }

    public function removeLyric(Lyrics $lyric): static
    {
        if ($this->lyrics->removeElement($lyric)) {
            // set the owning side to null (unless already changed)
            if ($lyric->getSong() === $this) {
                $lyric->setSong(null);
            }
        }

        return $this;
    }
}
