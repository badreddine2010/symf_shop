<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("prods:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("prods:read")
     */
    private $refCde;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("prods:read")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     * @Groups("prods:read")
     */
    private $total;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("prods:read")
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity=OrderLine::class, mappedBy="orderNum", orphanRemoval=true)
     * @Groups("prods:read")
     */
    private $orderLines;

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefCde(): ?string
    {
        return $this->refCde;
    }

    public function setRefCde(string $refCde): self
    {
        $this->refCde = $refCde;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }
    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines[] = $orderLine;
            $orderLine->setOrderNum($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getOrderNum() === $this) {
                $orderLine->setOrderNum(null);
            }
        }

        return $this;
    }
}
