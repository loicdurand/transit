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

    #[ORM\Column(length: 50, unique: true)]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
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

    /**
     * @var Collection<int, Numero>
     */
    #[ORM\OneToMany(targetEntity: Numero::class, mappedBy: 'envoi', orphanRemoval: true, cascade: ['persist'])]
    private Collection $numeros;

    /**
     * @var Collection<int, Fichier>
     */
    #[ORM\OneToMany(targetEntity: Fichier::class, mappedBy: 'envoi', orphanRemoval: true, cascade: ['persist'])]
    private Collection $fichiers;

    #[ORM\Column(nullable: true)]
    private ?bool $archive = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $archivedAt = null;

    private int $percents;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DirectionEnvoi $direction = null;

    /**
     * @var Collection<int, PointParticulier>
     */
    #[ORM\OneToMany(targetEntity: PointParticulier::class, mappedBy: 'envoi', orphanRemoval: true)]
    private Collection $points_particuliers;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
        $this->numeros = new ArrayCollection();
        $this->fichiers = new ArrayCollection();
        $this->points_particuliers = new ArrayCollection();
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

    public function getPercentage(): int
    {
        $actions = $this->getActions();
        $statut = $this->getStatut();
        if ($statut->getLibelle() === 'Finalisé') {
            $this->percents = 100;
            return $this->percents;
        }

        $this->percents = 0;

        foreach ($actions as $action) {
            $action_etape = $action->getEtape();
            $action_etape_statut = $action_etape->getStatutSiNegatif();
            if ($action_etape_statut->getId() === $statut->getId()) {
                $this->percents = round($action->getRang() / count($actions) * 100);
            }
        }

        return $this->percents;
    }

    /**
     * @return Collection<int, Numero>
     */
    public function getNumeros(): Collection
    {
        return $this->numeros;
    }

    public function addNumero(Numero $numero): static
    {
        if (!$this->numeros->contains($numero)) {
            $this->numeros->add($numero);
            $numero->setEnvoi($this);
        }

        return $this;
    }

    // public function removeNumero(Numero $numero): static
    // {
    //     if ($this->numeros->removeElement($numero)) {
    //         // set the owning side to null (unless already changed)
    //         if ($numero->getEnvoi() === $this) {
    //             $numero->setEnvoi(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Fichier>
     */
    public function getFichiers(): Collection
    {
        return $this->fichiers;
    }

    public function addFichier(Fichier $fichier): static
    {
        if (!$this->fichiers->contains($fichier)) {
            $this->fichiers->add($fichier);
            $fichier->setEnvoi($this);
        }

        return $this;
    }

    public function removeFichier(Fichier $fichier): static
    {
        if ($this->fichiers->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getEnvoi() === $this) {
                $fichier->setEnvoi(null);
            }
        }

        return $this;
    }

    public function isArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(?bool $archive): static
    {
        $this->archive = $archive;
        $this->setArchivedAt();

        return $this;
    }

    public function getArchivedAt(): ?\DateTime
    {
        return $this->archivedAt;
    }

    private function setArchivedAt(): static
    {
        $now = new \DateTime('now');
        $this->archivedAt = $now;

        return $this;
    }

    public function getDirection(): ?DirectionEnvoi
    {
        return $this->direction;
    }

    public function setDirection(?DirectionEnvoi $direction): static
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * @return Collection<int, PointParticulier>
     */
    public function getPointsParticuliers(): Collection
    {
        return $this->points_particuliers;
    }

    public function addPointsParticulier(PointParticulier $pointsParticulier): static
    {
        if (!$this->points_particuliers->contains($pointsParticulier)) {
            $this->points_particuliers->add($pointsParticulier);
            $pointsParticulier->setEnvoi($this);
        }

        return $this;
    }

    public function removePointsParticulier(PointParticulier $pointsParticulier): static
    {
        if ($this->points_particuliers->removeElement($pointsParticulier)) {
            // set the owning side to null (unless already changed)
            if ($pointsParticulier->getEnvoi() === $this) {
                $pointsParticulier->setEnvoi(null);
            }
        }

        return $this;
    }
}
