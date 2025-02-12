<?php

namespace App\Entity;

use App\Repository\SpellRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: SpellRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Spell
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', length: 1)]
    private string $keyboard;

    #[ORM\Column(type: 'string', length: 255)]
    private string $image;

    #[ORM\Column(type: 'json')]
    private array $cooldowns;

    #[ORM\ManyToMany(targetEntity: Champion::class, mappedBy: 'spells')]
    private Collection $champions;

    #[ORM\Column(type: 'string', length: 255)]
    private string $patch;

    #[ORM\Column(type: 'boolean')]
    private bool $affectedByCdr = true;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull]
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull]
    private DateTimeImmutable $updated_at;

    public function  __construct()
    {
        $this->champions = new ArrayCollection();
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updated_at = new DateTimeImmutable();
    }

    public function  getChampions(): Collection
    {
        return $this->champions;
    }

    public function addChampion(Champion $champion): self
    {
        if (!$this->champions->contains($champion)){
            $this->champions->add($champion);
            $champion->addSpell($this);
        }

        return $this;
    }

    public function removeChampion(Champion $champion): self
    {
        if ($this->champions->removeElement($champion)){
            $champion->removeSpell($this);
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getPatch(): string
    {
        return $this->patch->getNumero();
    }

    public function setPatch(string $patch): self
    {
        $this->patch = $patch;

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

    public function getKeyboard(): string
    {
        return $this->keyboard;
    }

    public function setKeyboard(string $keyboard): self
    {
        $this->keyboard = $keyboard;
        return $this;
    }

    public function getCooldowns(): array
    {
        return $this->cooldowns;
    }

    public function setCooldowns(array $cooldowns): self
    {
        $this->cooldowns = $cooldowns;

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

    public function isAffectedByCdr(): bool
    {
        return $this->affectedByCdr;
    }

    public function setAffectedByCdr(bool $affectedByCdr): self
    {
        $this->affectedByCdr = $affectedByCdr;
        return $this;
    }

}
