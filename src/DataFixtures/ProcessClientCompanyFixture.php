<?php

/*
 * This file is part of aakb/kontrolgruppen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;

/**
 * Class ProcessClientCompanyFixture.
 */
class ProcessClientCompanyFixture extends AbstractFixture implements DependentFixtureInterface
{
    protected $class = ProcessClientCompany::class;

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
