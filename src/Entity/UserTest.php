<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserTestRepository")
 */
class UserTest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fiveSecondTest;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFiveSecondTest(): ?bool
    {
        return $this->fiveSecondTest;
    }

    public function setFiveSecondTest(bool $fiveSecondTest): self
    {
        $this->fiveSecondTest = $fiveSecondTest;

        return $this;
    }
}
