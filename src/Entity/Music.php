<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MusicRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MusicRepository::class)]
class Music
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllMusics", "getMusic"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAllMusics", "getMusic"])]
    private ?string $MusicTitle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["getAllMusics", "getMusic"])]
    private ?\DateTimeInterface $Released = null;

    #[ORM\Column]
    #[Groups(["getAllMusics", "getMusic"])]
    private ?bool $status = null;

    #[ORM\ManyToMany(targetEntity: Genre::class)]
    private Collection $GenreId;

    #[ORM\ManyToOne]
    private ?Albums $albumsId = null;

    public function __construct()
    {
        $this->GenreId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMusicTitle(): ?string
    {
        return $this->MusicTitle;
    }

    public function setMusicTitle(string $MusicTitle): self
    {
        $this->MusicTitle = $MusicTitle;

        return $this;
    }

    public function getReleased(): ?\DateTimeInterface
    {
        return $this->Released;
    }

    public function setReleased(\DateTimeInterface $Released): self
    {
        $this->Released = $Released;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenreId(): Collection
    {
        return $this->GenreId;
    }

    public function addGenreId(Genre $genreId): self
    {
        if (!$this->GenreId->contains($genreId)) {
            $this->GenreId->add($genreId);
        }

        return $this;
    }

    public function removeGenreId(Genre $genreId): self
    {
        $this->GenreId->removeElement($genreId);

        return $this;
    }

    public function getAlbumsId(): ?Albums
    {
        return $this->albumsId;
    }

    public function setAlbumsId(?Albums $albumsId): self
    {
        $this->albumsId = $albumsId;

        return $this;
    }
}
