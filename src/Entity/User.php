<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    CONST AVAILABLE_ROLES=[
        "baseRole" => "ROLE_USER",
        "verifiedRole" => "ROLE_VERIFIED",
        "adminRole" => "ROLE_ADMIN"
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [self::AVAILABLE_ROLES["baseRole"]];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Products::class)]
    private Collection $Merchandise;

    public function __construct()
    {
        $this->Merchandise = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::AVAILABLE_ROLES["baseRole"];

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        if($isVerified){
            $this->setRoles([self::AVAILABLE_ROLES["verifiedRole"]]);
        }

        return $this;
    }

    /**
     * @return Collection<int, Products>
     */
    public function getMerchandise(): Collection
    {
        return $this->Merchandise;
    }

    public function addMerchandise(Products $merchandise): static
    {
        if (!$this->Merchandise->contains($merchandise)) {
            $this->Merchandise->add($merchandise);
            $merchandise->setSeller($this);
        }

        return $this;
    }

    public function removeMerchandise(Products $merchandise): static
    {
        if ($this->Merchandise->removeElement($merchandise)) {
            // set the owning side to null (unless already changed)
            if ($merchandise->getSeller() === $this) {
                $merchandise->setSeller(null);
            }
        }

        return $this;
    }

    public function __tostring(){
        return $this->username;
    }
}
