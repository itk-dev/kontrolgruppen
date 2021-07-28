<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use Kontrolgruppen\CoreBundle\Entity\User;

/**
 * Class UserFixture.
 */
class UserFixture extends AbstractFixture
{
    protected $class = User::class;
}
