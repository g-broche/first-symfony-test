<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
// use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
// #[ApiResource]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name_category = null;

    #[ORM\OneToMany(mappedBy: 'category_id', targetEntity: Products::class)]
    private Collection $related_products;

    public function __construct()
    {
        $this->related_products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCategory(): ?string
    {
        return $this->name_category;
    }

    public function setNameCategory(string $name_category): static
    {
        $this->name_category = $name_category;

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
            $relatedProduct->setCategoryId($this);
        }

        return $this;
    }

    public function removeRelatedProduct(Products $relatedProduct): static
    {
        if ($this->related_products->removeElement($relatedProduct)) {
            // set the owning side to null (unless already changed)
            if ($relatedProduct->getCategoryId() === $this) {
                $relatedProduct->setCategoryId(null);
            }
        }

        return $this;
    }

    
    public function __toString()
    {
        return $this->name_category;
    }
}
