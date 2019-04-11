<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PayementRepository")
 */
class Payement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $familyName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $codePostal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $creditCardNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $codeSecurity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cbExpire;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $payementPaid;

    /**
     * @ORM\Column(type="float")
     */
    private $totalPrice;

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

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): self
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getCreditCardNumber(): ?int
    {
        return $this->creditCardNumber;
    }

    public function setCreditCardNumber(int $creditCardNumber): self
    {
        $this->creditCardNumber = $creditCardNumber;

        return $this;
    }

    public function getCodeSecurity(): ?int
    {
        return $this->codeSecurity;
    }

    public function setCodeSecurity(int $codeSecurity): self
    {
        $this->codeSecurity = $codeSecurity;

        return $this;
    }

    public function getCbExpire(): ?\DateTimeInterface
    {
        return $this->cbExpire;
    }

    public function setCbExpire(\DateTimeInterface $cbExpire): self
    {
        $this->cbExpire = $cbExpire;

        return $this;
    }

    public function getPayementPaid(): ?bool
    {
        return $this->payementPaid;
    }

    public function setPayementPaid(bool $payementPaid): self
    {
        $this->payementPaid = $payementPaid;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }
}
