<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnimalRepository")
 */
class Animal
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
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sex;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $okDogs;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $okCats;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $okKids;

    /**
     * @ORM\Column(type="float")
     */
    private $adoptionPrice;

    /**
     * @ORM\Column(type="date")
     */
    private $dateAdd;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $needCare;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOkDogs(): ?bool
    {
        return $this->okDogs;
    }

    public function setOkDogs(?bool $okDogs): self
    {
        $this->okDogs = $okDogs;

        return $this;
    }

    public function getOkCats(): ?bool
    {
        return $this->okCats;
    }

    public function setOkCats(?bool $okCats): self
    {
        $this->okCats = $okCats;

        return $this;
    }

    public function getOkKids(): ?bool
    {
        return $this->okKids;
    }

    public function setOkKids(?bool $okKids): self
    {
        $this->okKids = $okKids;

        return $this;
    }

    public function getAdoptionPrice(): ?float
    {
        return $this->adoptionPrice;
    }

    public function setAdoptionPrice(float $adoptionPrice): self
    {
        $this->adoptionPrice = $adoptionPrice;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getFur(): ?string
    {
        return $this->fur;
    }

    public function setFur(?string $fur): self
    {
        $this->fur = $fur;

        return $this;
    }

    public function getNeedCare(): ?bool
    {
        return $this->needCare;
    }

    public function setNeedCare(bool $needCare): self
    {
        $this->needCare = $needCare;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}