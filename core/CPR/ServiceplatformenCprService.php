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
    private $dataFordelerController;
    private $httpClient;

    /**
     * ServiceplatformenCprService constructor.
     *
     * @param DatafordelerController $dataFordelerController
     * @param HttpClientInterface $httpClient
     */
    public function __construct(DatafordelerController $dataFordelerController, HttpClientInterface $httpClient)
    {
        $this->dataFordelerController = $dataFordelerController;
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Cpr $cpr): CprServiceResultInterface
    {
        try {
            $response = $this->dataFordelerController->getPersonData($cpr, $this->httpClient);
        } catch (ServiceException $e) {
            throw new CprException($e->getMessage(), $e->getCode(), $e);
        }

        return new ServiceplatformenCprServiceResult($response);
    }
}
