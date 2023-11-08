<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\LogEntry;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\VisitationLogEntryRepository")
 */
class VisitationLogEntry extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\visitation", inversedBy="logEntries")
     *
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $visitation;

    // /**
    //  * @ORM\ManyToOne(targetEntity="Gedmo\Loggable\Entity\LogEntry")
    //  * @ORM\JoinColumn(nullable=false)
    //  */
    // private $logEntry;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $creatorName;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $tableName;

    /**
     * @return Visitation|null
     */
    public function getVisitation(): ?Visitation
    {
        return $this->visitation;
    }

    /**
     * @param Visitation|null $visitation
     *
     * @return VisitationLogEntry
     */
    public function setVisitation(?Visitation $visitation): self
    {
        $this->visitation = $visitation;

        return $this;
    }

    // /**
    //  * @return LogEntry|null
    //  */
    // public function getLogEntry(): ?LogEntry
    // {
    //     return $this->logEntry;
    // }

    // /**
    //  * @param LogEntry|null $logEntry
    //  *
    //  * @return VisitationLogEntry
    //  */
    // public function setLogEntry(?LogEntry $logEntry): self
    // {
    //     $this->logEntry = $logEntry;

    //     return $this;
    // }

    /**
     * Set creator name.
     *
     * @param string|null $name
     *
     * @return $this
     */
    public function setCreatorName(?string $name): self
    {
        $this->creatorName = $name;

        return $this;
    }

    /**
     * Get creator name.
     *
     * @return string|null
     */
    public function getCreatorName(): ?string
    {
        return $this->creatorName;
    }

    /**
     * Set Folded Table.
     *
     * @param string|null $tableName
     *
     * @return $this
     */
    public function setTableName(?string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Get creator name.
     *
     * @return string|null
     */
    public function getTableName(): ?string
    {
        return $this->tableName;
    }
}
