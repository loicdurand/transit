<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FichierRepository::class)]
class Fichier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $chemin = null;

    #[ORM\ManyToOne(inversedBy: 'fichier')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Envoi $envoi = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChemin(): ?string
    {
        return $this->chemin;
    }

    public function setChemin(string $chemin): static
    {
        $this->chemin = $chemin;

        return $this;
    }

    public function getEnvoi(): ?Envoi
    {
        return $this->envoi;
    }

    public function setEnvoi(?Envoi $envoi): static
    {
        $this->envoi = $envoi;

        return $this;
    }
}
