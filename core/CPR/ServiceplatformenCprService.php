<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use ItkDev\Serviceplatformen\Service\Exception\ServiceException;
use Kontrolgruppen\CoreBundle\Controller\DatafordelerController;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class ServiceplatformenCprService.
 */
class ServiceplatformenCprService extends AbstractCprService implements CprServiceInterface
{
    private $service;
    private $httpClient;

    /**
     * ServiceplatformenCprService constructor.
     *
     * @param PersonBaseDataExtendedService $service
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Cpr $cpr): CprServiceResultInterface
    {
        return new ServiceplatformenCprServiceResult([]);
    }
}
