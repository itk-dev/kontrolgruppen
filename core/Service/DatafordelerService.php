<?php

namespace Kontrolgruppen\CoreBundle\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DatafordelerService
{
    private $datafordelerHttpClient;

    public function __construct(HttpClientInterface $datafordelerHttpClient)
    {
        $this->datafordelerHttpClient = $datafordelerHttpClient;
    }

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

        $data = $response->toArray()['Personer'][0]['Person'];
        if ($cprAdresse = $data['Adresseoplysninger'][0]['Adresseoplysninger']['CprAdresse']) {
            $data['Bopaelssamling'] = $this->getBopaelssamling($cprAdresse, $data['Personnumre'][0]['Personnummer']['personnummer'], $data['Navne'][0]['Navn']['adresseringsnavn'], $this->datafordelerHttpClient);
        }

        return $data;
    }

    /**
     * @param array               $cprAdresse
     * @param string              $relationCpr
     * @param string              $relationFullname
     * @param HttpClientInterface $datafordelerHttpClient
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
     * @param string              $cvr
     * @param HttpClientInterface $datafordelerHttpClient
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

        if (404 === $response->getStatusCode()) {
            return [];
        }

        return $response->toArray();
    }

    /**
     * @param string              $pnumber
     * @param HttpClientInterface $datafordelerHttpClient
     *
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    public function getVirksomhedDataByPNumber(string $pnumber, HttpClientInterface $datafordelerHttpClient): array
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

        if (404 === $response->getStatusCode()) {
            return [];
        }

        return $response->toArray();
    }
}
