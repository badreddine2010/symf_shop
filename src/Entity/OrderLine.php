<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderLineRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OrderLineRepository::class)
 */
class OrderLine
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("prods:read")
     * @Groups("orders:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderLines")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $orderNum;

    /**
     * @ORM\Column(type="integer")
     * @Groups("prods:read")
     * @Groups("orders:read")
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     * @Groups("prods:read")
     * @Groups("orders:read")
     */
    private $total;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups("prods:read")
     * @Groups("orders:read")
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getOrderNum(): ?Order
    {
        return $this->orderNum;
    }

    public function setOrderNum(?Order $orderNum): self
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

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
}
