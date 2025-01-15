<?php

namespace App\Entity;

use App\Repository\CoasterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoasterRepository::class)]
class Coaster
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: 'La vitesse maximale doit être un nombre positif.')]
    private ?int $maxSpeed = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: 'La longeur maximale doit être un nombre positif.')]
    private ?int $length = null;

    #[ORM\Column(nullable: false)]
    #[Assert\Positive(message: 'La hauteur maximale doit être un nombre positif.')]
    private ?int $maxHeight = null;

    #[ORM\Column]
    private ?bool $operating = true;

    #[ORM\ManyToOne(inversedBy: 'coasters')]
    #[Assert\NotBlank(message: 'Un Park doit être sélectionné.')]
    private ?Park $Park = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'Coasters')]
    private Collection $Categories;

    #[ORM\ManyToOne(inversedBy: 'coasters')]
    private ?User $author = null;

    #[ORM\Column(nullable: true)]
    private ?bool $published = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFileName = null;

    public function __construct()
    {
        $this->Categories = new ArrayCollection();
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

    public function getMaxSpeed(): ?int
    {
        return $this->maxSpeed;
    }

    public function setMaxSpeed(?int $maxSpeed): static
    {
        $this->maxSpeed = $maxSpeed;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getMaxHeight(): ?int
    {
        return $this->maxHeight;
    }

    public function setMaxHeight(?int $maxHeight): static
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    public function isOperating(): ?bool
    {
        return $this->operating ?? false;
    }

    public function setOperating(bool $operating): static
    {
        $this->operating = $operating;

        return $this;
    }

    public function getPark(): ?Park
    {
        return $this->Park;
    }

    public function setPark(?Park $Park): static
    {
        $this->Park = $Park;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->Categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->Categories->contains($category)) {
            $this->Categories->add($category);
            $category->addCoaster($this); // Assurez la cohérence bidirectionnelle
        }

        return $this;
    }


    public function removeCategory(Category $category): static
    {
        $this->Categories->removeElement($category);

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published ?? false;
    }

    public function setPublished(?bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(?string $imageFileName): static
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }
}
