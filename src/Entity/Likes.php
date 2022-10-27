<?php

namespace App\Entity;

use App\Repository\LikesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikesRepository::class)]
class Likes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_User = null;

    #[ORM\Column]
    private ?int $id_Music = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?int
    {
        return $this->id_User;
    }

    public function setIdUser(int $id_User): self
    {
        $this->id_User = $id_User;

        return $this;
    }

    public function getIdMusic(): ?int
    {
        return $this->id_Music;
    }

    public function setIdMusic(int $id_Music): self
    {
        $this->id_Music = $id_Music;

        return $this;
    }
}
