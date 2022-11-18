<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'isLike')]
    private Collection $id_user;

    #[ORM\ManyToMany(targetEntity: Music::class, inversedBy: 'islike')]
    private Collection $id_music;

    public function __construct()
    {
        $this->id_user = new ArrayCollection();
        $this->id_music = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getIdUser(): Collection
    {
        return $this->id_user;
    }

    public function addIdUser(User $idUser): self
    {
        if (!$this->id_user->contains($idUser)) {
            $this->id_user->add($idUser);
        }

        return $this;
    }

    public function removeIdUser(User $idUser): self
    {
        $this->id_user->removeElement($idUser);

        return $this;
    }

    /**
     * @return Collection<int, Music>
     */
    public function getIdMusic(): Collection
    {
        return $this->id_music;
    }

    public function addIdMusic(Music $idMusic): self
    {
        if (!$this->id_music->contains($idMusic)) {
            $this->id_music->add($idMusic);
        }

        return $this;
    }

    public function removeIdMusic(Music $idMusic): self
    {
        $this->id_music->removeElement($idMusic);

        return $this;
    }
}
