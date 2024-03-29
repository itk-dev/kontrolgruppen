<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Kontrolgruppen\CoreBundle\BBR\BBRKoder;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Datafordeler Service.
 */
class DatafordelerService
{
    private $datafordelerCprHttpClient;
    private $datafordelerCvrHttpClient;
    private $httpClient;

    /**
     * @param HttpClientInterface $datafordelerCprHttpClient
     * @param HttpClientInterface $datafordelerCvrHttpClient
     * @param HttpClientInterface $httpClient
     */
    public function __construct(?HttpClientInterface $datafordelerCprHttpClient, ?HttpClientInterface $datafordelerCvrHttpClient, HttpClientInterface $httpClient = null)
    {
        $this->datafordelerCprHttpClient = $datafordelerCprHttpClient;
        $this->datafordelerCvrHttpClient = $datafordelerCvrHttpClient;
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    /**
     * @param string $cpr
     *
     * @return array
     *
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function getPersonData(string $cpr): array
    {
        $response = $this->datafordelerCprHttpClient->request(
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

        $data['stamdata'] = $this->getStamdata($data);

        $adresseringsnavn = '';
        foreach ($data['Navne'] as $navn) {
            if (isset($navn['Navn']['adresseringsnavn'])) {
                $adresseringsnavn = $navn['Navn']['adresseringsnavn'];
                break;
            }
        }
        if ($cprAdresse = $data['Adresseoplysninger'][0]['Adresseoplysninger']['CprAdresse']) {
            $data['Bopaelssamling'] = $this->getBopaelssamling($cprAdresse, $data['Personnumre'][0]['Personnummer']['personnummer'], $adresseringsnavn);
        }

        $data['BBR'] = $this->getBBR($cprAdresse);

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getStamdata(array $data): array
    {
        $adress = [
            $data['Adresseoplysninger'][0]['Adresseoplysninger']['CprAdresse']['vejadresseringsnavn'] ?? null,
            $data['Adresseoplysninger'][0]['Adresseoplysninger']['CprAdresse']['husnummer'] ?? null,
            $data['Adresseoplysninger'][0]['Adresseoplysninger']['CprAdresse']['etage'] ?? null,
            $data['Adresseoplysninger'][0]['Adresseoplysninger']['CprAdresse']['sidedoer'] ?? null,
        ];

        $city = [
            $data['Adresseoplysninger'][0]['Adresseoplysninger']['CprAdresse']['postnummer'] ?? null,
            $data['Adresseoplysninger'][0]['Adresseoplysninger']['CprAdresse']['postdistrikt'] ?? null,
        ];

        $navnObject = null;
        foreach ($data['Navne'] as $navn) {
            $navnObject = $navn['Navn'];

            if (!isset($navnObject['virkningTil'])) {
                break;
            }
        }

        return [
            'navn' => $this->getFullnameFromNameObject($navnObject),
            'cpr' => $data['Personnumre'][0]['Personnummer']['personnummer'] ?? '',
            'adresse' => implode(' ', array_filter($adress)),
            'by' => implode(' ', array_filter($city)),
            'koen' => $data['koen'] ?? 'Ukendt',
        ];
    }

    /**
     * @param array $cprAdresse
     *
     * @return array
     */
    public function getBBR(array $cprAdresse): array
    {
        $query = ['struktur' => 'mini'];
        if (isset($cprAdresse['vejadresseringsnavn'])) {
            $query['vejnavn'] = $cprAdresse['vejadresseringsnavn'];
        }
        if (isset($cprAdresse['husnummer'])) {
            $query['husnr'] = ltrim($cprAdresse['husnummer'], '0');
        }
        if (isset($cprAdresse['etage'])) {
            $query['etage'] = ltrim($cprAdresse['etage'], '0');
        }
        if (isset($cprAdresse['sidedoer'])) {
            $query['dør'] = $cprAdresse['sidedoer'];
        }
        if (isset($cprAdresse['postnummer'])) {
            $query['postnr'] = $cprAdresse['postnummer'];
        }
        if (isset($cprAdresse['cprKommunekode'])) {
            $query['kommunekode'] = $cprAdresse['cprKommunekode'];
        }
        if (isset($cprAdresse['cprVejkode'])) {
            $query['vejkode'] = $cprAdresse['cprVejkode'];
        }

        $response = $this->httpClient->request(
            'GET',
            'https://api.dataforsyningen.dk/adresser',
            [
                'query' => $query,
            ]
        );

        if (404 === $response->getStatusCode()) {
            return [];
        }

        $data = $response->toArray();

        if (!isset($data[0]['id'])) {
            return [];
        }

        $adresseId = $data[0]['id'];

        $response = $this->httpClient->request(
            'GET',
            'https://nyt.ois.dk/api/property/GetBFEFromAddressId',
            [
                'query' => ['addressId' => $adresseId],
            ]
        );

        try {
            if (404 === $response->getStatusCode()) {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }

        $bfe = $response->getContent();

        $response = $this->httpClient->request(
            'GET',
            'https://nyt.ois.dk/api/property/GetPropertyFromBFE',
            [
                'query' => ['bfe' => $bfe],
            ]
        );

        try {
            if (404 === $response->getStatusCode()) {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }

        $data = $response->toArray();

        $bbr = [];
        foreach ($data['enhed'] as $enhed) {
            if ($enhed['ENHDR_id_lokalId'] === $adresseId) {
                $bbr['type'] = BBRKoder::BYG_ANVENDELSE[$enhed['enh020EnhedensAnvendelse']];
                $bbr['boligareal'] = $enhed['enh027ArealTilBeboelse'];
                $bbr['bygningsareal'] = array_column($data['bygning'], null, 'Id')[array_column($data['opgang'], null, 'Id')[$enhed['OPG_id_lokalId']]['BYG_id_lokalId']]['byg041BebyggetAreal'];
                $bbr['vaerelser'] = $enhed['enh031AntalVærelser'];
                $bbr['toiletter'] = $enhed['enh065AntalVandskylledeToiletter'];
                $bbr['bygninger'] = \count($data['bygning']);
                $bbr['ois_link'] = 'https://nyt.ois.dk/search/'.$bfe.'/sfe/bbr/enhed/'.$enhed['Id'];
                $bbr['adressebetegnelse'] = $enhed['adressebetegnelse'];

                return $bbr;
            }
        }

        return [];
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
        $response = $this->datafordelerCprHttpClient->request(
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
            if (isset($person['Person']['Foraelderoplysninger'])) {
                foreach ($person['Person']['Foraelderoplysninger'] as $fop) {
                    if ($fop['Foraelderoplysning']['Foraelder']['Navn']['adresseringsnavn'] === $relationFullname) {
                        $relation = 'Barn';
                    }
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
        $fornavne = $navn['fornavne'] ?? null;
        $mellemnavn = $navn['mellemnavn'] ?? null;
        $efternavn = $navn['efternavn'] ?? null;

        return implode(' ', array_filter([$fornavne, $mellemnavn, $efternavn]));
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
        $response = $this->datafordelerCvrHttpClient->request(
            'GET',
            'CVR/HentCVRDataFortrolig/1/rest/hentVirksomhedMedCVRNummerFortrolig',
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
        foreach ($data['produktionsenheder'] as $key => $value) {
            if ($key >= 5) {
                break;
            }
            try {
                // add to data['p-numre']
                $data['pNummer'][] = $this->getVirksomhedDataByPNumber($value['pNummer']);
            } catch (\Exception $e) {
                throw new \Exception('Cpr data kan ikke findes', 1);
            }
        }

        if (404 === $response->getStatusCode()) {
            return [];
        }

        return $data;
    }

    /**
     * @param string $pnumber
     *
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    public function getVirksomhedDataByPNumber(string $pnumber): array
    {
        $response = $this->datafordelerCvrHttpClient->request(
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
