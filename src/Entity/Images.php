<?php

namespace App\Entity;

use App\Repository\ImagesRepository;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $imageFileName = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Product", inversedBy: "images")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product;
    
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isMain = false;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $_delete = false;


    private $file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(string $imageFileName): static
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function getDelete(): ?bool
    {
        return $this->_delete;
    }

    public function setDelete(?bool $_delete): self
    {
        $this->_delete = $_delete;

        return $this;
    }

    public function getIsMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(?bool $isMain): self
    {
        $this->isMain = $isMain;

        return $this;
    }

}
