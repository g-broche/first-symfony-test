<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name_product = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description_product = null;

    #[ORM\Column]
    private ?float $price_product = null;

    #[ORM\Column]
    private ?bool $availability_product = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?References $reference_id = null;

    #[ORM\ManyToOne(inversedBy: 'related_products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $category_id = null;

    #[ORM\ManyToMany(targetEntity: Distributors::class, inversedBy: 'related_products')]
    private Collection $product_distributors;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_product = null;

    public function __construct()
    {
        $this->product_distributors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameProduct(): ?string
    {
        return $this->name_product;
    }

    public function setNameProduct(string $name_product): static
    {
        $this->name_product = $name_product;

        return $this;
    }

    public function getDescriptionProduct(): ?string
    {
        return $this->description_product;
    }

    public function setDescriptionProduct(string $description_product): static
    {
        $this->description_product = $description_product;

        return $this;
    }

    public function getPriceProduct(): ?float
    {
        return $this->price_product;
    }

    public function setPriceProduct(float $price_product): static
    {
        $this->price_product = $price_product;

        return $this;
    }

    public function isAvailabilityProduct(): ?bool
    {
        return $this->availability_product;
    }

    public function setAvailabilityProduct(bool $availability_product): static
    {
        $this->availability_product = $availability_product;

        return $this;
    }

    public function getReferenceId(): ?References
    {
        return $this->reference_id;
    }

    public function setReferenceId(References $reference_id): static
    {
        $this->reference_id = $reference_id;

        return $this;
    }

    public function getCategoryId(): ?Categories
    {
        return $this->category_id;
    }

    public function setCategoryId(?Categories $category_id): static
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * @return Collection<int, Distributors>
     */
    public function getProductDistributors(): Collection
    {
        return $this->product_distributors;
    }

    public function addProductDistributor(Distributors $productDistributor): static
    {
        if (!$this->product_distributors->contains($productDistributor)) {
            $this->product_distributors->add($productDistributor);
        }

        return $this;
    }

    public function removeProductDistributor(Distributors $productDistributor): static
    {
        $this->product_distributors->removeElement($productDistributor);

        return $this;
    }

    public function getImageProduct(): ?string
    {
        return $this->image_product;
    }

    public function setImageProduct(?string $image_product): static
    {
        $this->image_product = $image_product;

        return $this;
    }
}
