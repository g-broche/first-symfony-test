<?php

namespace App\Entity;

use App\Repository\DistributorsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
// use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: DistributorsRepository::class)]
// #[ApiResource]
class Distributors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name_distributor = null;

    #[ORM\ManyToMany(targetEntity: Products::class, mappedBy: 'product_distributors')]
    private Collection $related_products;

    public function __construct()
    {
        $this->related_products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameDistributor(): ?string
    {
        return $this->name_distributor;
    }

    public function setNameDistributor(string $name_distributor): static
    {
        $this->name_distributor = $name_distributor;

        return $this;
    }

    /**
     * @return Collection<int, Products>
     */
    public function getRelatedProducts(): Collection
    {
        return $this->related_products;
    }

    public function addRelatedProduct(Products $relatedProduct): static
    {
        if (!$this->related_products->contains($relatedProduct)) {
            $this->related_products->add($relatedProduct);
            $relatedProduct->addProductDistributor($this);
        }

        return $this;
    }

    public function removeRelatedProduct(Products $relatedProduct): static
    {
        if ($this->related_products->removeElement($relatedProduct)) {
            $relatedProduct->removeProductDistributor($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name_distributor;
    }
}
