<?php

namespace App\Entity;

use App\Repository\StaffRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StaffRepository::class)]
class Staff
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $workStartDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $workLeftDate = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $socialSecurityNumber = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $citizenshipNumber = null;

    #[ORM\Column(length: 66)]
    private ?string $createdBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 66, nullable: true)]
    private ?string $changedBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $changedAt = null;

    #[ORM\Column]
    private ?bool $deleted = false;

    #[ORM\Column(length: 66, nullable: true)]
    private ?string $deletedBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\OneToMany(mappedBy: 'staff', targetEntity: AnnualPermit::class)]
    private Collection $annualPermits;

    public function __construct()
    {
        $this->annualPermits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getWorkStartDate(): ?\DateTimeInterface
    {
        return $this->workStartDate;
    }

    public function setWorkStartDate(\DateTimeInterface $workStartDate): self
    {
        $this->workStartDate = $workStartDate;

        return $this;
    }

    public function getWorkLeftDate(): ?\DateTimeInterface
    {
        return $this->workLeftDate;
    }

    public function setWorkLeftDate(?\DateTimeInterface $workLeftDate): self
    {
        $this->workLeftDate = $workLeftDate;

        return $this;
    }

    public function getSocialSecurityNumber(): ?string
    {
        return $this->socialSecurityNumber;
    }

    public function setSocialSecurityNumber(string $socialSecurityNumber): self
    {
        $this->socialSecurityNumber = $socialSecurityNumber;

        return $this;
    }

    public function getCitizenshipNumber(): ?string
    {
        return $this->citizenshipNumber;
    }

    public function setCitizenshipNumber(string $citizenshipNumber): self
    {
        $this->citizenshipNumber = $citizenshipNumber;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getChangedBy(): ?string
    {
        return $this->changedBy;
    }

    public function setChangedBy(?string $changedBy): self
    {
        $this->changedBy = $changedBy;

        return $this;
    }

    public function getChangedAt(): ?\DateTimeInterface
    {
        return $this->changedAt;
    }

    public function setChangedAt(?\DateTimeInterface $changedAt): self
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeletedBy(): ?string
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?string $deletedBy): self
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection<int, AnnualPermit>
     */
    public function getAnnualPermits(): Collection
    {
        return $this->annualPermits;
    }

    public function addAnnualPermit(AnnualPermit $annualPermit): self
    {
        if (!$this->annualPermits->contains($annualPermit)) {
            $this->annualPermits->add($annualPermit);
            $annualPermit->setStaff($this);
        }

        return $this;
    }

    public function removeAnnualPermit(AnnualPermit $annualPermit): self
    {
        if ($this->annualPermits->removeElement($annualPermit)) {
            // set the owning side to null (unless already changed)
            if ($annualPermit->getStaff() === $this) {
                $annualPermit->setStaff(null);
            }
        }

        return $this;
    }

}
