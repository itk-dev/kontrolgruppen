<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;

/**
 * Class ProcessClientPersonFixture.
 */
class ProcessClientPersonFixture extends AbstractFixture implements DependentFixtureInterface
{
    protected $class = ProcessClientPerson::class;

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            ProcessFixture::class,
        ];
    }
}
