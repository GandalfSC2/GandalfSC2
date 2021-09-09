<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TvshowRepository::class)
 */
class Tvshow
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $synopsis;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbLikes;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $publishedAt;

    /**
     * @ORM\OneToMany(targetEntity=season::class, mappedBy="relation")
     */
    private $relation;

    /**
     * @ORM\ManyToMany(targetEntity=character::class, inversedBy="relationToTvshow")
     */
    private $relationToCharacter;

    /**
     * @ORM\ManyToMany(targetEntity=category::class)
     */
    private $relationToCategory;

    public function __construct()
    {
        $this->relation = new ArrayCollection();
        $this->relationToCharacter = new ArrayCollection();
        $this->relationToCategory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getNbLikes(): ?int
    {
        return $this->nbLikes;
    }

    public function setNbLikes(?int $nbLikes): self
    {
        $this->nbLikes = $nbLikes;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection|season[]
     */
    public function getRelation(): Collection
    {
        return $this->relation;
    }

    public function addRelation(season $relation): self
    {
        if (!$this->relation->contains($relation)) {
            $this->relation[] = $relation;
            $relation->setRelation($this);
        }

        return $this;
    }

    public function removeRelation(season $relation): self
    {
        if ($this->relation->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getRelation() === $this) {
                $relation->setRelation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|character[]
     */
    public function getRelationToCharacter(): Collection
    {
        return $this->relationToCharacter;
    }

    public function addRelationToCharacter(character $relationToCharacter): self
    {
        if (!$this->relationToCharacter->contains($relationToCharacter)) {
            $this->relationToCharacter[] = $relationToCharacter;
        }

        return $this;
    }

    public function removeRelationToCharacter(character $relationToCharacter): self
    {
        $this->relationToCharacter->removeElement($relationToCharacter);

        return $this;
    }

    /**
     * @return Collection|category[]
     */
    public function getRelationToCategory(): Collection
    {
        return $this->relationToCategory;
    }

    public function addRelationToCategory(category $relationToCategory): self
    {
        if (!$this->relationToCategory->contains($relationToCategory)) {
            $this->relationToCategory[] = $relationToCategory;
        }

        return $this;
    }

    public function removeRelationToCategory(category $relationToCategory): self
    {
        $this->relationToCategory->removeElement($relationToCategory);

        return $this;
    }
}
