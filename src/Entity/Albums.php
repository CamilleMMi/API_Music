<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AlbumsRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AlbumsRepository::class)]
class Albums
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllGenres", "getAlbums"])]
    private ?int $id = null;

    #[Groups(["getAllGenres", "getAlbums"])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(["getAllGenres", "getAlbums"])]
    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[Groups(["getAllGenres", "getAlbums"])]
    #[ORM\Column(length: 255)]
    private ?string $musics = null;

    #[Groups(["getAllGenres", "getAlbums"])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[Groups(["getAllGenres", "getAlbums"])]
    #[ORM\Column]
    private ?bool $status = null;

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

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getMusics(): ?string
    {
        return $this->musics;
    }

    public function setMusics(string $musics): self
    {
        $this->musics = $musics;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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
}
