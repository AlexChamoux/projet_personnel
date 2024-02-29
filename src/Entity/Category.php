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

    #[ORM\Column(length: 255)]
    private ?string $nameCategory = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $imageCategory = null;

    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: "category")]
    private Collection $products;


    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCategory(): ?string
    {
        return $this->nameCategory;
    }

    public function setNameCategory(string $nameCategory): static
    {
        $this->nameCategory = $nameCategory;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImageCategory(): ?string
    {
        return $this->imageCategory;
    }

    public function setImageCategory(string $imageCategory): static
    {
        $this->imageCategory = $imageCategory;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    // public function addProduct(Product $product): static
    // {
    //     if (!$this->products->contains($product)) {
    //         $this->products->add($product);
    //         $product->addCategory($this);
    //     }

    //     return $this;
    // }

    // public function removeProduct(Product $product): static
    // {
    //     if ($this->products->removeElement($product)) {
    //         $product->removeCategory($this);
    //     }

    //     return $this;
    // }

    public function __toString()
    {
        return $this->nameCategory;
    }
}
