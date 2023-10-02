<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DatafordelerController extends BaseController
{
    /**
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    protected function getPersonData(string $cpr, HttpClientInterface $httpClient): array
    {
        $response = $httpClient->request(
            'GET',
            $this->getParameter('datafordeler_url') . 'CPR/CprPersonFullComplete/1/rest/PersonFullCurrentListComplete',
            [
                'query' => [
                    'pnr.personnummer.eq' => $cpr,
                ],
            ]
        );

        if ($response->getStatusCode() === 404) {
            return [];
        }

        return $response->toArray();
    }

        /**
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    protected function getVirksomhedData(string $cvr, HttpClientInterface $httpClient): array
    {
        $response = $httpClient->request(
            'GET',
            $this->getParameter('datafordeler_url') . 'CVR/n',
            [
                'query' => [
                    'pnr.cvr.eq' => $cvr,
                ],
            ]
        );

        if ($response->getStatusCode() === 404) {
            return [];
        }

        return $response->toArray();
    }
}
