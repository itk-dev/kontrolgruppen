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
 * Base class for clients on a visitation.
 *
 * @ORM\Entity()
 * @ORM\Table(name="visitation_client")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 * })
 *
 * @Gedmo\Loggable()
 */
abstract class AbstractVisitationClient extends AbstractEntity implements VisitationLoggableInterface
{
    public const COMPANY = 'company';
    public const PERSON = 'person';

    // Overwritten in subclasses.
    public const TYPE = null;

    /**
     * @ORM\OneToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Visitation", inversedBy="visitationClient", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $visitation;

    /**
     * The actual instance type matching the "discr" value.
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Gedmo\Versioned()
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $name;

    /**
     * AbstractVisitationClient constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get client type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get visitation.
     *
     * @return Visitation|null
     */
    public function getVisitation(): ?Visitation
    {
        return $this->visitation;
    }

    /**
     * Set visitation.
     *
     * @param Visitation $visitation
     *
     * @return $this
     */
    public function setVisitation(Visitation $visitation): self
    {
        $this->visitation = $visitation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     *
     * @return AbstractVisitationClient
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
