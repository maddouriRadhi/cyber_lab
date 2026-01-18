<?php

namespace App\Entity;

use App\Repository\TeacherApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherApplicationRepository::class)]
class TeacherApplication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 140)]
    private string $fullName = '';

    #[ORM\Column(length: 180)]
    private string $email = '';

    #[ORM\Column(length: 60)]
    private string $primaryTrack = '';

    #[ORM\Column(length: 30)]
    private string $experienceLevel = '';

    #[ORM\Column(type: 'text')]
    private string $bio = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $links = null;

    #[ORM\Column(length: 20)]
    private string $status = 'NEW';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getFullName(): string { return $this->fullName; }
    public function setFullName(string $fullName): self { $this->fullName = $fullName; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPrimaryTrack(): string { return $this->primaryTrack; }
    public function setPrimaryTrack(string $primaryTrack): self { $this->primaryTrack = $primaryTrack; return $this; }

    public function getExperienceLevel(): string { return $this->experienceLevel; }
    public function setExperienceLevel(string $experienceLevel): self { $this->experienceLevel = $experienceLevel; return $this; }

    public function getBio(): string { return $this->bio; }
    public function setBio(string $bio): self { $this->bio = $bio; return $this; }

    public function getLinks(): ?string { return $this->links; }
    public function setLinks(?string $links): self { $this->links = $links; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function __toString(): string
    {
        return $this->fullName.' ('.$this->email.')';
    }
}
