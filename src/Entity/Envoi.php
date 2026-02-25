<?php

namespace App\Entity;

use App\Repository\EnvoiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EnvoiRepository::class)]
#[UniqueEntity(
    'titre',
    message: 'Ce titre a déjà été utilisé. Veuillez choisir un titre unique.',
    errorPath: 'titre'
)]
class Envoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25, unique: true)]
    private ?string $titre = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Destinataire $destinataire = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Objet $objet = null;

    #[ORM\ManyToOne]
    private ?TypeEnvoi $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatutEnvoi $statut = null;

    /**
     * @var Collection<int, Action>
     */
    #[ORM\OneToMany(targetEntity: Action::class, mappedBy: 'envoi', orphanRemoval: true)]
    private Collection $actions;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDestinataire(): ?Destinataire
    {
        return $this->destinataire;
    }

    public function setDestinataire(?Destinataire $destinataire): static
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    public function getObjet(): ?Objet
    {
        return $this->objet;
    }

    public function setObjet(?Objet $objet): static
    {
        $this->objet = $objet;

        return $this;
    }

    public function getType(): ?TypeEnvoi
    {
        return $this->type;
    }

    public function setType(?TypeEnvoi $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getStatut(): ?StatutEnvoi
    {
        return $this->statut;
    }

    public function setStatut(?StatutEnvoi $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Action>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): static
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
            $action->setEnvoi($this);
        }

        return $this;
    }

    // public function removeAction(Action $action): static
    // {
    //     if ($this->actions->removeElement($action)) {
    //         // set the owning side to null (unless already changed)
    //         if ($action->getEnvoi() === $this) {
    //             $action->setEnvoi(null);
    //         }
    //     }

    //     return $this;
    // }
}
