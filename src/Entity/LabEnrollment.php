<?php

namespace App\Entity;

use App\Repository\LabEnrollmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LabEnrollmentRepository::class)]
#[ORM\Table(name: 'lab_enrollment')]
#[ORM\UniqueConstraint(name: 'uniq_lab_student', columns: ['lab_id', 'student_id'])]
class LabEnrollment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Lab::class, inversedBy: 'enrollments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Lab $lab = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $student = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getLab(): ?Lab { return $this->lab; }
    public function setLab(Lab $lab): self { $this->lab = $lab; return $this; }

    public function getStudent(): ?User { return $this->student; }
    public function setStudent(User $student): self { $this->student = $student; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
