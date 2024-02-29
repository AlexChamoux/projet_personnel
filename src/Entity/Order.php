<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_order = null;

    #[ORM\Column]
    private ?int $ordered_item_id = null;

    #[ORM\Column(length: 255)]
    private ?string $ordered_item_name = null;

    #[ORM\Column(length: 255)]
    private ?string $pathMainImage = null;

    #[ORM\Column]
    private ?int $priceUnit = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $total = null;

    #[ORM\Column]
    private ?int $total_order = null;

    #[ORM\Column]
    private ?int $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getDateOrder(): ?\DateTimeInterface
    {
        return $this->date_order;
    }

    public function setDateOrder(\DateTimeInterface $date_order): static
    {
        $this->date_order = $date_order;

        return $this;
    }

    public function getOrderedItemId(): ?string
    {
        return $this->ordered_item_id;
    }

    public function setOrderedItemId(string $ordered_item_id): static
    {
        $this->ordered_item_id = $ordered_item_id;

        return $this;
    }

    public function getOrderedItemName(): ?string
    {
        return $this->ordered_item_name;
    }

    public function setOrderedItemName(string $ordered_item_name): static
    {
        $this->ordered_item_name = $ordered_item_name;

        return $this;
    }
    
    public function getPathMainImage(): ?string
    {
        return $this->pathMainImage;
    }

    public function setPathMainImage(?string $pathMainImage): self
    {
        $this->pathMainImage = $pathMainImage;

        return $this;
    }

    public function getPriceUnit(): int
    {
        return $this->priceUnit;
    }

    public function setPriceUnit(int $priceUnit): self
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getTotalOrder(): int
    {
        return $this->total_order;
    }

    public function setTotalOrder(int $totalOrder): self
    {
        $this->total_order = $totalOrder;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }
}
