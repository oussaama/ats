<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductsRepository")
 */
class Products implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productName;

    /**
     * @ORM\Column(type="float")
     */
    private $basePrise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productMaterial;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $Delivery;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Details;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reviews",mappedBy="product",cascade={"persist", "remove"}))
     */
    private $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function  jsonSerialize()
    {
        return (object)['productName'=>$this->getProductName()];
    }

    public function addProduct($productName,$basePrise,$category,$brand,$productMaterial,$imageUrl,$Delivery,$details){
        $this->setProductName($productName);
        $this->setBasePrise($basePrise);
        $this->setCategory($category);
        $this->setBrand($brand);
        $this->setProductMaterial($productMaterial);
        $this->setImageUrl($imageUrl);
        $this->setDelivery($Delivery);
        $this->setDetails($details);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getBasePrise(): ?float
    {
        return $this->basePrise;
    }

    public function setBasePrise(float $basePrise): self
    {
        $this->basePrise = $basePrise;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getProductMaterial(): ?string
    {
        return $this->productMaterial;
    }

    public function setProductMaterial(string $productMaterial): self
    {
        $this->productMaterial = $productMaterial;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }


    public function getDetails(): ?string
    {
        return $this->Details;
    }

    public function setDetails(string $Details): self
    {
        $this->Details = $Details;

        return $this;
    }
    

    /**
     * @return Collection|Reviews[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }

    public function getDelivery(): ?string
    {
        return $this->Delivery;
    }

    public function setDelivery(string $Delivery): self
    {
        $this->Delivery = $Delivery;

        return $this;
    }
    
}
