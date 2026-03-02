<?php

namespace App\Entity;

use App\Repository\PointParticulierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointParticulierRepository::class)]
class PointParticulier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatutPointParticulier $statut = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[ORM\Column]
    private ?int $quantite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?StatutPointParticulier
    {
        return $this->statut;
    }

    public function setStatut(?StatutPointParticulier $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }
}
