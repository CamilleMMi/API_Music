<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AlbumsRepository;
// use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Groups;
// use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;

// @Hateoas\Relation(
//     "self",
//     href= @Hateoas\Route(
//         "albums.get",
//         parameters = { "idAlbums" = "expr(object.getId())" }
//     ),
//     exclusion = @Hateoas\Exclusion(groups="getAlbums")
// )

// @Hateoas\Relation(
//     "self",
//     href= @Hateoas\Route(
//         "albums.getAll",
//     ),
//     exclusion = @Hateoas\Exclusion(groups="getAllAlbums")
// )


// Hateoas\Relation(
// "self",
// href= @Hateoas\Route("albums.get", parameters = { "idAlbums" = "expr(object.getId())" }),
// exclusion = @Hateoas\Exclusion(groups="getAlbums");
// )

#[ORM\Entity(repositoryClass: AlbumsRepository::class)]
class Albums
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllAlbums", "getAlbums"])]
    private ?int $id = null;

    #[Groups(["getAllAlbums", "getAlbums"])]
    // #[Assert\NotNull(message:"Un albums doit avoir un intitulé")]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(["getAllAlbums", "getAlbums"])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[Groups(["getAllAlbums", "getAlbums"])]
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
