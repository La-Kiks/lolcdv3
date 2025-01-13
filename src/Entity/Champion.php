<?php

namespace App\Entity;

use App\Repository\ChampionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ChampionRepository::class)]
class Champion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 9)]
    private string $customId;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $alias;

    #[ORM\Column(type: 'string', length: 255)]
    private string $image;

    #[ORM\ManyToMany(targetEntity: Spell::class, inversedBy: 'champions')]
    #[ORM\JoinTable(name: 'champion_spell')]
    private Collection $spells;

    public function __construct()
    {
        $this->spells = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpells(): Collection
    {
        return $this->spells;
    }

    public function addSpell(Spell $spell): self
    {
        if(!$this->spells->contains($spell)){
            $this->spells->add($spell);
        }
        return $this;
    }

    public function removeSpell(Spell $spell): self
    {
        if ($this->spells->contains($spell)){
            $this->spells->removeElement($spell);
            $spell->removeChampion($this);
        }
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCustomId(): string
    {
        return $this->customId;
    }

    public function setCustomId(string $customId): self
    {
        $this->customId = $customId;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;
        return $this;
    }


}
