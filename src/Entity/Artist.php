<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $whatsapp = null;

    #[ORM\Column(length: 255)]
    private ?string $instagram = null;

    #[ORM\Column(length: 255)]
    private ?string $facebook = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    /**
     * @var Collection<int, ArtistImage>
     */
    #[ORM\OneToMany(targetEntity: ArtistImage::class, mappedBy: 'Artist', orphanRemoval: true)]
    private Collection $artistImage;

    #[ORM\Column(length: 255)]
    private ?string $nameSyllables = null;

    #[ORM\Column]
    private ?int $position = null;

    public function __construct()
    {
        $this->artistImage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getWhatsapp(): ?string
    {
        return $this->whatsapp;
    }

    public function setWhatsapp(string $whatsapp): static
    {
        $this->whatsapp = $whatsapp;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(string $instagram): static
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(string $facebook): static
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, ArtistImage>
     */
    public function getArtistImage(): Collection
    {
        return $this->artistImage;
    }

    public function addArtistImage(ArtistImage $artistImage): static
    {
        if (!$this->artistImage->contains($artistImage)) {
            $this->artistImage->add($artistImage);
            $artistImage->setArtist($this);
        }

        return $this;
    }

    public function removeArtistImage(ArtistImage $artistImage): static
    {
        if ($this->artistImage->removeElement($artistImage)) {
            // set the owning side to null (unless already changed)
            if ($artistImage->getArtist() === $this) {
                $artistImage->setArtist(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName(); 
    }

    public function getNameSyllables(): ?string
    {
        return $this->nameSyllables;
    }

    public function setNameSyllables(string $nameSyllables): static
    {
        $this->nameSyllables = $nameSyllables;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}
