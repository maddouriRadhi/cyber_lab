<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_item')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderRef = null;

    #[ORM\Column(length: 60)]
    private string $sku = '';

    #[ORM\Column(length: 140)]
    private string $name = '';

    #[ORM\Column]
    private int $unitPriceCents = 0;

    #[ORM\Column]
    private int $qty = 1;

    public function getId(): ?int { return $this->id; }

    public function getOrderRef(): ?Order { return $this->orderRef; }
    public function setOrderRef(Order $order): self { $this->orderRef = $order; return $this; }

    public function getSku(): string { return $this->sku; }
    public function setSku(string $sku): self { $this->sku = $sku; return $this; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getUnitPriceCents(): int { return $this->unitPriceCents; }
    public function setUnitPriceCents(int $cents): self { $this->unitPriceCents = $cents; return $this; }

    public function getQty(): int { return $this->qty; }
    public function setQty(int $qty): self { $this->qty = max(1, $qty); return $this; }
}
