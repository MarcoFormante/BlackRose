<?php

namespace App\Entity;

use App\Repository\OpeningTimeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OpeningTimeRepository::class)]
class OpeningTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lunedi = null;

    #[ORM\Column(length: 255)]
    private ?string $sabato = null;

    #[ORM\Column(length: 255)]
    private ?string $domenica = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLunedi(): ?string
    {
        return $this->lunedi;
    }

    public function setLunedi(string $lunedi): static
    {
        $this->lunedi = $lunedi;

        return $this;
    }

    public function getSabato(): ?string
    {
        return $this->sabato;
    }

    public function setSabato(string $sabato): static
    {
        $this->sabato = $sabato;

        return $this;
    }

    public function getDomenica(): ?string
    {
        return $this->domenica;
    }

    public function setDomenica(string $domenica): static
    {
        $this->domenica = $domenica;

        return $this;
    }
}
