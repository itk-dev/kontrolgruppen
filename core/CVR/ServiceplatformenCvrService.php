<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CVR;

/**
 * Class ServiceplatformenCvrService.
 */
class ServiceplatformenCvrService extends AbstractCvrService implements CvrServiceInterface
{
    private $service;

    /**
     * {@inheritdoc}
     */
    public function find(Cvr $cvr): CvrServiceResultInterface
    {
        return new ServiceplatformenCvrServiceResult([]);
    }
}
