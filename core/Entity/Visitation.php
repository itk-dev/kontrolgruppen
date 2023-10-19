<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\VisitationRepository")
 *
 * @Gedmo\Loggable()
 */ class Visitation extends AbstractEntity
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\VisitationLogEntry", mappedBy="process", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $logEntries;
    /**
     * @ORM\OneToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\AbstractVisitationClient", mappedBy="visitation", cascade={"persist", "remove"})
     */
    private $visitationClient;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $type;

    /**
     * Visitation constructor.
     */
    public function __construct()
    {
        $this->logEntries = new ArrayCollection();
    }

    /**
     * @return AbstractVisitationClient|null
     */
    public function getVisitationClient(): ?AbstractVisitationClient
    {
        return $this->visitationClient;
    }

    /**
     * @param AbstractVisitationClient $visitationClient
     *
     * @return Visitation
     */
    public function setVisitationClient(AbstractVisitationClient $visitationClient): self
    {
        $this->visitationClient = $visitationClient;

        // set the owning side of the relation if necessary
        if ($this !== $visitationClient->getVisitation()) {
            $visitationClient->setVisitation($this);
        }

        return $this;
    }

    /**
     * @param Conclusion|null $conclusion
     * /**
     * @return Collection|VisitationLogEntry[]
     */
    public function getLogEntries(): Collection
    {
        return $this->logEntries;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param string|null $identifier
     *
     * @return Visitation
     */
    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return Visitation
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
