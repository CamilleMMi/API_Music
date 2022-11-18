<?php

namespace App\Entity;

use App\Repository\AuthorsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorsRepository::class)]
class Authors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAuthors", "getAllAuthors"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAuthors", "getAllAuthors"])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Albums::class)]
    #[Groups(["getAuthors"])]
    private Collection $albumsid;

    #[ORM\ManyToMany(targetEntity: Music::class)]
    #[Groups(["getAuthors"])]
    private Collection $musicsId;

    #[ORM\Column]
    private ?bool $status = null;

    public function __construct()
    {
        $this->albumsid = new ArrayCollection();
        $this->musicsId = new ArrayCollection();
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
     * @return Collection<int, Albums>
     */
    public function getAlbumsid(): Collection
    {
        return $this->albumsid;
    }

    public function addAlbumsid(Albums $albumsid): self
    {
        if (!$this->albumsid->contains($albumsid)) {
            $this->albumsid->add($albumsid);

        }
        return $this;
    }

    public function removeAlbumsid(Albums $albumsid): self
    {
        $this->albumsid->removeElement($albumsid);

        return $this;
    }

    /**
     * @return Collection<int, Music>
     */
    public function getMusicsId(): Collection
    {
        return $this->musicsId;
    }

    public function addMusicsId(Music $musicsId): self
    {
        if (!$this->musicsId->contains($musicsId)) {
            $this->musicsId->add($musicsId);
        }

        return $this;
    }

    public function removeMusicsId(Music $musicsId): self
    {
        $this->musicsId->removeElement($musicsId);

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
