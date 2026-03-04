<?php

namespace App\Entity;

use App\Repository\NumeroRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NumeroRepository::class)]
class Numero
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'numeros')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Envoi $envoi = null;

    #[ORM\Column(length: 50)]
    private ?string $libelle = null;

    #[ORM\Column(length: 50)]
    private ?string $valeur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    // public function getEnvoi(): ?Envoi
    // {
    //     return $this->envoi;
    // }

    public function setEnvoi(?Envoi $envoi): static
    {
        $this->envoi = $envoi;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): static
    {
        $this->valeur = $valeur;

        return $this;
    }
}
