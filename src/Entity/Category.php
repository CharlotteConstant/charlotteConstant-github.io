<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @UniqueEntity(
 *  fields={"name"},
 * message="oh fan des purges, cette catégorie existe deja voyons!")
 */
class Category
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
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

  

    /**
     * @ORM\OneToMany(targetEntity=Pokemon::class, mappedBy="categorie", orphanRemoval=true)
     */
    private $lesPokemons;

    public function __construct()
    {
        $this->pokemons = new ArrayCollection();
        $this->lesPokemons = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


    /**
     * @return Collection|Pokemon[]
     */
    public function getLesPokemons(): Collection
    {
        return $this->lesPokemons;
    }

    public function addLesPokemon(Pokemon $lesPokemon): self
    {
        if (!$this->lesPokemons->contains($lesPokemon)) {
            $this->lesPokemons[] = $lesPokemon;
            $lesPokemon->setCategorie($this);
        }

        return $this;
    }

    public function removeLesPokemon(Pokemon $lesPokemon): self
    {
        if ($this->lesPokemons->removeElement($lesPokemon)) {
            // set the owning side to null (unless already changed)
            if ($lesPokemon->getCategorie() === $this) {
                $lesPokemon->setCategorie(null);
            }
        }

        return $this;
    }
}
