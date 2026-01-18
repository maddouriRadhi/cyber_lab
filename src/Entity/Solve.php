<?php

namespace App\Entity;

use App\Repository\SolveRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SolveRepository::class)]
#[ORM\Table(name: 'solve', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_user_lab', columns: ['user_id', 'lab_id'])
])]
class Solve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'solves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'solves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lab $lab = null;

    #[ORM\Column]
    private \DateTimeImmutable $solvedAt;

    public function __construct()
    {
        $this->solvedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    // ✅ allow null for orphanRemoval remove
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getLab(): ?Lab
    {
        return $this->lab;
    }

    public function setLab(?Lab $lab): self
    {
        $this->lab = $lab;
        return $this;
    }

    public function getSolvedAt(): \DateTimeImmutable
    {
        return $this->solvedAt;
    }

    public function setSolvedAt(\DateTimeImmutable $date): self
    {
        $this->solvedAt = $date;
        return $this;
    }
}
