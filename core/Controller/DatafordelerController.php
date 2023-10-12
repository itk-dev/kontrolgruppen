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
    protected function getPersonData(string $cpr, HttpClientInterface $datafordelerHttpClient): array
    {
        $response = $datafordelerHttpClient->request(
            'GET',
            'CPR/CprPersonFullComplete/1/rest/PersonFullCurrentListComplete',
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
    protected function getVirksomhedData(string $cvr, HttpClientInterface $datafordelerHttpClient): array
    {
        $response = $datafordelerHttpClient->request(
            'GET',
            'CVR/HentCVRData/1/rest/hentVirksomhedMedCVRNummer',
            [
                'query' => [
                    'pCVRNummer' => $cvr,
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
    protected function getVirksomhedDataByPNumber(string $pnumber, HttpClientInterface $datafordelerHttpClient): array
    {
        $response = $datafordelerHttpClient->request(
            'GET',
            'CVR/HentCVRData/1/rest/hentProduktionsenhedMedPNummer',
            [
                'query' => [
                    'ppNummer' => $pnumber,
                ],
            ]
        );

        if ($response->getStatusCode() === 404) {
            return [];
        }

        return $response->toArray();
    }
}
