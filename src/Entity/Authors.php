<?php

namespace App\Entity;

use App\Repository\AuthorsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorsRepository::class)]
class Authors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $albums = null;

    #[ORM\Column(length: 255)]
    private ?string $musics = null;

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

    public function getAlbums(): ?string
    {
        return $this->albums;
    }

    public function setAlbums(string $albums): self
    {
        $this->albums = $albums;

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
}
