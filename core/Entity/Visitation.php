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
 
 class Visitation
 {
     private $completedAt;
     private $processType;
     private $processStatus;
     private $reminders;
     private $journalEntries;
     private $visitationClient;
     private $conclusion;
     private $economyEntries;
     private $logEntries;
     private $policeReport;
     private $courtDecision;
     private $lockedNetValue;
     private $visitedByCaseWorker = false;
     private $lockedNetValues;
     private $forwardedToAuthorities;
     private $revenueEntries;
     private $processGroups;
     private $lastCompletedAt;
     private $lastReopened;
     private $lastNetCollectiveSum;
     private $netCollectiveSumDifference;
     private $originallyCompletedAt;
 
     public function __construct()
     {
         $this->reminders = new ArrayCollection();
         $this->journalEntries = new ArrayCollection();
         $this->economyEntries = new ArrayCollection();
         $this->logEntries = new ArrayCollection();
         $this->lockedNetValues = new ArrayCollection();
         $this->forwardedToAuthorities = new ArrayCollection();
         $this->revenueEntries = new ArrayCollection();
         $this->processGroups = new ArrayCollection(); 
    }

    /**
     * @return \DateTime|null
     */
    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

        /**
     * @return AbstractProcessClient|null
     */
    public function getVisitationClient(): ?AbstractProcessClient
    {
        return $this->visitationClient;
    }

        /**
     * @param AbstractProcessClient $processClient
     *
     * @return Process
     */
    public function setVisitationClient(AbstractProcessClient $processClient): self
    {
        $this->visitationClient = $processClient;

        return $this;
    }

    /**
     * @param \DateTime|null $completedAt
     *
     * @return Visitation
     */
    public function setCompletedAt(?\DateTime $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }



    /**
     * @return ProcessType|null
     */
    public function getProcessType(): ?ProcessType
    {
        return $this->processType;
    }

    /**
     * @param ProcessType|null $processType
     *
     * @return Visitation
     */
    public function setProcessType(?ProcessType $processType): self
    {
        $this->processType = $processType;

        return $this;
    }

    /**
     * @return ProcessStatus|null
     */
    public function getProcessStatus(): ?ProcessStatus
    {
        return $this->processStatus;
    }

    /**
     * @param ProcessStatus|null $processStatus
     *
     * @return Visitation
     */
    public function setProcessStatus(?ProcessStatus $processStatus): self
    {
        $this->processStatus = $processStatus;

        return $this;
    }

    /**
     * @return Collection|Reminder[]
     */
    public function getReminders(): Collection
    {
        return $this->reminders;
    }


    /**
     * @return Collection|JournalEntry[]
     */
    public function getJournalEntries(): Collection
    {
        return $this->journalEntries;
    }


    /**
     * @return Conclusion|null
     */
    public function getConclusion(): ?Conclusion
    {
        return $this->conclusion;
    }

    /**
     * @param Conclusion|null $conclusion
     * 

    /**
     * @return Collection|ProcessLogEntry[]
     */
    public function getLogEntries(): Collection
    {
        return $this->logEntries;
    }

    /**
     * @return bool|null
     */
    public function getCourtDecision(): ?bool
    {
        return $this->courtDecision;
    }

    /**
     * @param bool|null $courtDecision
     *
     * @return Visitation
     */
    public function setCourtDecision(?bool $courtDecision): self
    {
        $this->courtDecision = $courtDecision;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLockedNetValue(): ?float
    {
        return $this->lockedNetValue;
    }

    /**
     * @param float|null $lockedNetValue
     *
     * @return Visitation
     */
    public function setLockedNetValue(?float $lockedNetValue): self
    {
        $this->lockedNetValue = $lockedNetValue;

        return $this;
    }

    /**
     * @return bool
     */
    public function getVisitedByCaseWorker(): bool
    {
        return $this->visitedByCaseWorker;
    }

    /**
     * @param bool $visited
     */
    public function setVisitedByCaseWorker(bool $visited)
    {
        $this->visitedByCaseWorker = $visited;
    }

    /**
     * @return Collection|LockedNetValue[]
     */
    public function getLockedNetValues(): Collection
    {
        return $this->lockedNetValues;
    }

    /**
     * @param LockedNetValue $lockedNetValue
     *
     * @return Visitation
     */
    public function removeLockedNetValue(LockedNetValue $lockedNetValue): self
    {
        if ($this->lockedNetValues->contains($lockedNetValue)) {
            $this->lockedNetValues->removeElement($lockedNetValue);
            // set the owning side to null (unless already changed)
            if ($lockedNetValue->getProcess() === $this) {
                $lockedNetValue->setProcess(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getLastCompletedAt(): ?\DateTimeInterface
    {
        return $this->lastCompletedAt;
    }

    /**
     * @param \DateTimeInterface|null $lastCompletedAt
     *
     * @return $this
     */
    public function setLastCompletedAt(?\DateTimeInterface $lastCompletedAt): self
    {
        $this->lastCompletedAt = $lastCompletedAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getLastReopened(): ?\DateTimeInterface
    {
        return $this->lastReopened;
    }

    /**
     * @param \DateTimeInterface|null $lastReopened
     *
     * @return $this
     */
    public function setLastReopened(?\DateTimeInterface $lastReopened): self
    {
        $this->lastReopened = $lastReopened;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLastNetCollectiveSum(): ?float
    {
        return $this->lastNetCollectiveSum;
    }

    /**
     * @param float|null $lastNetCollectiveSum
     *
     * @return $this
     */
    public function setLastNetCollectiveSum(?float $lastNetCollectiveSum): self
    {
        $this->lastNetCollectiveSum = $lastNetCollectiveSum;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getNetCollectiveSumDifference(): ?float
    {
        return $this->netCollectiveSumDifference;
    }

    /**
     * @param float|null $netCollectiveSumDifference
     *
     * @return $this
     */
    public function setNetCollectiveSumDifference(?float $netCollectiveSumDifference): self
    {
        $this->netCollectiveSumDifference = $netCollectiveSumDifference;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getOriginallyCompletedAt(): ?\DateTimeInterface
    {
        return $this->originallyCompletedAt;
    }

    /**
     * @param \DateTimeInterface|null $originallyCompletedAt
     *
     * @return $this
     */
    public function setOriginallyCompletedAt(?\DateTimeInterface $originallyCompletedAt): self
    {
        $this->originallyCompletedAt = $originallyCompletedAt;

        return $this;
    }
}
