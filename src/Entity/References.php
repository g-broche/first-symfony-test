<?php

namespace App\Entity;

use App\Repository\ReferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReferencesRepository::class)]
#[ORM\Table(name: '`references`')]
class References
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $number_reference = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberReference(): ?string
    {
        return $this->number_reference;
    }

    public function setNumberReference(string $number_reference): static
    {
        $this->number_reference = $number_reference;

        return $this;
    }

    public function __toString()
    {
    return $this->number_reference;
        
    }
}
