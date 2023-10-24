<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CVR;

use ItkDev\Serviceplatformen\Service\Exception\ServiceException;

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
        try {
            $response = $this->service->getLegalUnit($cvr);
        } catch (ServiceException $e) {
            throw new CvrException($e->getMessage(), $e->getCode(), $e);
        }

        return new ServiceplatformenCvrServiceResult($response);
    }
}
