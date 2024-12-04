<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $name = null;

    #[ORM\Column(length: 7)]
    private ?string $color = null;

    /**
     * @var Collection<int, Coaster>
     */
    #[ORM\ManyToMany(targetEntity: Coaster::class, mappedBy: 'Categories')]
    private Collection $Coasters;

    public function __construct()
    {
        $this->Coasters = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, Coaster>
     */
    public function getCoasters(): Collection
    {
        return $this->Coasters;
    }

    public function addCoaster(Coaster $coaster): static
    {
        if (!$this->Coasters->contains($coaster)) {
            $this->Coasters->add($coaster);
            $coaster->addCategory($this);
        }

        return $this;
    }

    public function removeCoaster(Coaster $coaster): static
    {
        if ($this->Coasters->removeElement($coaster)) {
            $coaster->removeCategory($this);
        }

        return $this;
    }
}
