<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class GetConclusionTypesEvent.
 */
class GetConclusionTypesEvent extends Event
{
    const NAME = 'kontrolgruppen.core.get_conclusion_types';

    private $types;

    /**
     * GetConclusionTypesEvent constructor.
     *
     * @param array $types
     */
    public function __construct(array $types = [])
    {
        $this->types = $types;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param $types
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }
}
