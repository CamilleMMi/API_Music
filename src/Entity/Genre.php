<?php

namespace App\Entity;

use Assert\NotNull;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GenreRepository;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Compenent\Validator\Constraint as Assert;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllGenres", "getGenre"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAllGenres", "getGenre"])]
    #[Assert\NotNull(message: "A genre must have a name")]
    private ?string $GenreName = null;

    #[ORM\Column]
    #[Groups(["getAllGenres", "getGenre"])]
    private ?bool $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGenreName(): ?string
    {
        return $this->GenreName;
    }

    public function setGenreName(string $GenreName): self
    {
        $this->GenreName = $GenreName;

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
