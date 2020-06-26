<?php

namespace App\Entity;

use App\Repository\ProgrammeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProgrammeRepository::class)
 */
class Programme
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
    private $annee;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $theme;

    /**
     * @ORM\OneToMany(targetEntity=ProgrammeActivite::class, mappedBy="programme")
     */
    private $activites;

    public function __construct()
    {
        $this->activites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return Collection|ProgrammeActivite[]
     */
    public function getActivites(): Collection
    {
        return $this->activites;
    }

    public function addActivite(ProgrammeActivite $activite): self
    {
        if (!$this->activites->contains($activite)) {
            $this->activites[] = $activite;
            $activite->setProgramme($this);
        }

        return $this;
    }

    public function removeActivite(ProgrammeActivite $activite): self
    {
        if ($this->activites->contains($activite)) {
            $this->activites->removeElement($activite);
            // set the owning side to null (unless already changed)
            if ($activite->getProgramme() === $this) {
                $activite->setProgramme(null);
            }
        }

        return $this;
    }
}
