<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'uniq_user_email', columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $email = '';

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private string $password = '';

    #[ORM\Column(length: 120)]
    private string $fullName = '';

    #[ORM\Column(options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Solve::class, orphanRemoval: true)]
    private Collection $solves;

    public function __construct()
    {
        $this->solves = new ArrayCollection();
        $this->isActive = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // --- Security ---
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /** @deprecated */
    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void {}

    // --- Fields ---
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = mb_strtolower(trim($email));
        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = trim($fullName);
        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->fullName !== '' ? $this->fullName : $this->email;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    // ✅ optional alias (if your UserChecker calls isEnabled())
    public function isEnabled(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $active): self
    {
        $this->isActive = $active;
        return $this;
    }

    // ✅ Always inject ROLE_USER so students can access dashboard even if DB roles=[]
    public function getRoles(): array
    {
        $roles = $this->roles;

        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return array_values(array_unique($roles));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_values(array_unique($roles));
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    // --- Solves ---
    public function getSolves(): Collection
    {
        return $this->solves;
    }

    public function addSolve(Solve $solve): self
    {
        if (!$this->solves->contains($solve)) {
            $this->solves->add($solve);
            $solve->setUser($this);
        }
        return $this;
    }

    public function removeSolve(Solve $solve): self
    {
        if ($this->solves->removeElement($solve)) {
            // orphanRemoval=true → keep relation consistent
            if ($solve->getUser() === $this) {
                $solve->setUser(null);
            }
        }
        return $this;
    }
}
