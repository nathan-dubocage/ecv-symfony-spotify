<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = "Unknown";

    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'songs')]
    private iterable $artists = [];

    #[ORM\Column(type: 'string', length: 255)]
    private string $file = '';

    private ?File $formFile = null;

    #[ORM\Column(type: 'integer')]
    private int $duration = 0;

    #[ORM\Column(type: 'integer')]
    private int $listeningnumber = 0;

    #[ORM\Column(type: 'string', length: 255)]
    private string $image = '';

    private ?File $imageFile = null;

    #[ORM\ManyToMany(targetEntity: Playlist::class, mappedBy: 'songs')]
    private $playlists;

    public function __construct()
    {
        $this->artists = new ArrayCollection();
        $this->playlists = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    public function addArtist(Artist $artist): self
    {
        if (!$this->artists->contains($artist)) {
            $this->artists[] = $artist;
        }

        return $this;
    }

    public function removeArtist(Artist $artist): self
    {
        $this->artists->removeElement($artist);

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getListeningnumber(): ?int
    {
        return $this->listeningnumber;
    }

    public function setListeningnumber(int $listeningnumber): self
    {
        $this->listeningnumber = $listeningnumber;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getFormFile(): ?File
    {
        return $this->formFile;
    }
    public function setFormFile(?File $formFile)
    {
        $this->formFile = $formFile;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    public function setImageFile(?File $imageFile)
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists[] = $playlist;
            $playlist->addSong($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlists->removeElement($playlist)) {
            $playlist->removeSong($this);
        }

        return $this;
    }
}
