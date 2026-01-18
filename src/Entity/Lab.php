<?php

namespace App\Entity;

use App\Repository\LabRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LabRepository::class)]
#[ORM\Table(name: 'lab')]
#[ORM\HasLifecycleCallbacks]
class Lab
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    private string $description = '';

    #[ORM\Column(length: 20)]
    private string $difficulty = 'Easy';

    #[ORM\Column]
    private int $points = 50;

    #[ORM\Column(length: 255)]
    private string $flag = '';

    #[ORM\Column]
    private bool $published = true;

    #[ORM\Column(length: 160, unique: true)]
    private string $slug = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    // ✅ NEW: teacher owner of this lab
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $teacher = null;

    // ✅ NEW: live session scheduling
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $scheduledAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meetingLink = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxSeats = null;

    #[ORM\OneToMany(mappedBy: 'lab', targetEntity: Solve::class, orphanRemoval: true)]
    private Collection $solves;

    // ✅ NEW: enrollments
    #[ORM\OneToMany(mappedBy: 'lab', targetEntity: LabEnrollment::class, orphanRemoval: true)]
    private Collection $enrollments;

    public function __construct()
    {
        $this->solves = new ArrayCollection();
        $this->enrollments = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function ensureSlug(): void
    {
        if (trim($this->slug) === '' && trim($this->title) !== '') {
            $this->slug = self::slugify($this->title);
        }
    }

    private static function slugify(string $text): string
    {
        $text = trim($text);
        $text = mb_strtolower($text, 'UTF-8');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text;
        $text = preg_replace('~[^a-z0-9]+~', '-', $text) ?: '';
        $text = trim($text, '-');

        return $text !== '' ? $text : 'lab';
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getDifficulty(): string { return $this->difficulty; }
    public function setDifficulty(string $difficulty): self { $this->difficulty = $difficulty; return $this; }

    public function getPoints(): int { return $this->points; }
    public function setPoints(int $points): self { $this->points = $points; return $this; }

    public function getFlag(): string { return $this->flag; }
    public function setFlag(string $flag): self { $this->flag = $flag; return $this; }

    public function isPublished(): bool { return $this->published; }
    public function setPublished(bool $published): self { $this->published = $published; return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getSolves(): Collection { return $this->solves; }

    // ✅ Teacher
    public function getTeacher(): ?User { return $this->teacher; }
    public function setTeacher(?User $teacher): self { $this->teacher = $teacher; return $this; }

    // ✅ Scheduling
    public function getScheduledAt(): ?\DateTimeInterface { return $this->scheduledAt; }
    public function setScheduledAt(?\DateTimeInterface $scheduledAt): self { $this->scheduledAt = $scheduledAt; return $this; }

    public function getMeetingLink(): ?string { return $this->meetingLink; }
    public function setMeetingLink(?string $meetingLink): self { $this->meetingLink = $meetingLink; return $this; }

    public function getMaxSeats(): ?int { return $this->maxSeats; }
    public function setMaxSeats(?int $maxSeats): self { $this->maxSeats = $maxSeats; return $this; }

    public function getEnrollments(): Collection { return $this->enrollments; }

    public function __toString(): string
    {
        return $this->title;
    }
}
