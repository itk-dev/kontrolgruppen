<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Datafordeler Service.
 */
class DatafordelerService
{
    private $datafordelerHttpClient;

    /**
     * @param HttpClientInterface $datafordelerHttpClient
     */
    public function __construct(HttpClientInterface $datafordelerHttpClient)
    {
        $this->datafordelerHttpClient = $datafordelerHttpClient;
    }

    /**
     * @param string $cpr
     *
     * @return array
     *
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function getPersonData(string $cpr): array
    {
        $response = $this->datafordelerHttpClient->request(
            'GET',
            'CPR/CprPersonFullComplete/1/rest/PersonFullListComplete',
            [
                'query' => [
                    'pnr.personnummer.eq' => $cpr,
                ],
            ]
        );

        if (404 === $response->getStatusCode()) {
            return [];
        }

        try {
            $data = $response->toArray()['Personer'][0]['Person'];
        } catch (\Exception $e) {
            throw new \Exception('Cpr data kan ikke findes', 1);
        }
        $adresseringsnavn = null;
        foreach ($data['Navne'] as $navn) {
            if (isset($navn['Navn']['adresseringsnavn'])) {
                $adresseringsnavn = $navn['Navn']['adresseringsnavn'];
                break;
            }
        }
        if (!isset($adresseringsnavn)) {
            throw new \Exception('Adresseringsnavn er ikke defineret', 1);
        }
        if ($cprAdresse = $data['Adresseoplysninger'][0]['Adresseoplysninger']['CprAdresse']) {
            $data['Bopaelssamling'] = $this->getBopaelssamling($cprAdresse, $data['Personnumre'][0]['Personnummer']['personnummer'], $adresseringsnavn, $this->datafordelerHttpClient);
        }

        return $data;
    }

    /**
     * @param array  $cprAdresse
     * @param string $relationCpr
     * @param string $relationFullname
     *
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    public function getBopaelssamling(array $cprAdresse, string $relationCpr, string $relationFullname): array
    {
        $query = ['adropl.status.eq' => 'aktuel'];
        foreach ($cprAdresse as $key => $value) {
            $query['cadr.'.$key.'.eq'] = $value;
        }
        $response = $this->datafordelerHttpClient->request(
            'GET',
            'CPR/CprPersonFullComplete/1/rest/PersonFullCurrentListComplete',
            [
                'query' => $query,
            ]
        );

        if (404 === $response->getStatusCode()) {
            return [];
        }

        $data = $response->toArray()['Personer'];
        $bopaelssamling = [];
        foreach ($data as $person) {
            if ($person['Person']['Personnumre'][0]['Personnummer']['personnummer'] === $relationCpr) {
                continue;
            }

            $relation = 'Andet';
            foreach ($person['Person']['Foraelderoplysninger'] as $fop) {
                if ($fop['Foraelderoplysning']['Foraelder']['Navn']['adresseringsnavn'] === $relationFullname) {
                    $relation = 'Barn';
                }
            }
            foreach ($person['Person']['Civilstande'] as $civ) {
                if ('aktuel' === $civ['Civilstand']['status'] && isset($civ['Civilstand']['Aegtefaelle']) && $civ['Civilstand']['Aegtefaelle']['Navn']['adresseringsnavn'] === $relationFullname) {
                    $relation = 'Ægtefælle';
                }
            }

            $bopaelssamling[] = [
                'CPR' => $person['Person']['Personnumre'][0]['Personnummer']['personnummer'],
                'Navn' => $this->getFullnameFromNameObject($person['Person']['Navne'][0]['Navn']),
                'DatoFra' => $person['Person']['Adresseoplysninger'][0]['Adresseoplysninger']['virkningFra'],
                'Relation' => $relation,
            ];
        }

        return $bopaelssamling;
    }

    /**
     * @param array $navn
     *
     * @return string
     */
    public function getFullnameFromNameObject(array $navn): string
    {
        $fornavne = $navn['fornavne'];
        $mellemnavn = $navn['mellemnavn'] ?? '';
        $efternavn = $navn['efternavn'];

        return implode(' ', [$fornavne, $mellemnavn, $efternavn]);
    }

    /**
     * @param string $cvr
     *
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    public function getVirksomhedData(string $cvr): array
    {
        $response = $this->datafordelerHttpClient->request(
            'GET',
            'CVR/HentCVRData/1/rest/hentVirksomhedMedCVRNummer',
            [
                'query' => [
                    'pCVRNummer' => $cvr,
                ],
            ]
        );
        $data = $response->toArray();
        if (empty($data['virksomhed'])) {
            throw new \Exception('Empty data received', 1);
        }
        foreach ($data['produktionsenheder'] as $value) {
            try {
                // add to data['p-numre']
                $data['pNummer'][] = $this->getVirksomhedDataByPNumber($value['pNummer']);
            }catch (\Exception $e) {
                throw new \Exception('Cpr data kan ikke findes', 1);
            }
        }

        if (404 === $response->getStatusCode()) {
            return [];
        }

        return $data;
    }

    /**
     * @param string              $pnumber
     *
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    public function getVirksomhedDataByPNumber(string $pnumber): array
    {
        $response = $this->datafordelerHttpClient->request(
            'GET',
            'CVR/HentCVRData/1/rest/hentProduktionsenhedMedPNummer',
            [
                'query' => [
                    'ppNummer' => $pnumber,
                ],
            ]
        );

        if (404 === $response->getStatusCode()) {
            return [];
        }

        return $response->toArray();
    }
}
