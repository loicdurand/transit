<?php

namespace App\Entity;

use App\Repository\ActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;

#[ORM\Entity(repositoryClass: ActionRepository::class)]
class Action
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[OrderBy(['sortOrder' => 'ASC'])]
    private ?int $rang = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etape $etape = null;

    #[ORM\Column(nullable: true)]
    private ?bool $resultat = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    private ?Envoi $envoi = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    private ?Objet $objet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRang(): ?int
    {
        return $this->rang;
    }

    public function setRang(int $rang): static
    {
        $this->rang = $rang;

        return $this;
    }

    public function getEtape(): ?Etape
    {
        return $this->etape;
    }

    public function setEtape(?Etape $etape): static
    {
        $this->etape = $etape;

        return $this;
    }

    public function isResultat(): ?bool
    {
        return $this->resultat;
    }

    public function setResultat(?bool $resultat): static
    {
        $this->resultat = $resultat;

        return $this;
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

    // public function getObjet(): ?Objet
    // {
    //     return $this->objet;
    // }

    public function setObjet(?Objet $objet): static
    {
        $this->objet = $objet;

        return $this;
    }
}
