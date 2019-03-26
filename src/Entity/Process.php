<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProcessRepository")
 */
class Process
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="processes")
     */
    private $CaseWorker;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $CaseNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ClientCPR;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCaseWorker(): ?User
    {
        return $this->CaseWorker;
    }

    public function setCaseWorker(?User $CaseWorker): self
    {
        $this->CaseWorker = $CaseWorker;

        return $this;
    }

    public function getCaseNumber(): ?string
    {
        return $this->CaseNumber;
    }

    public function setCaseNumber(string $CaseNumber): self
    {
        $this->CaseNumber = $CaseNumber;

        return $this;
    }

    public function getClientCPR(): ?string
    {
        return $this->ClientCPR;
    }

    public function setClientCPR(string $ClientCPR): self
    {
        $this->ClientCPR = $ClientCPR;

        return $this;
    }
}
