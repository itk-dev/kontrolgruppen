<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Service\DatafordelerService;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/datafordeler")
 */
class DatafordelerController extends BaseController
{
    /**
     * @param string              $cpr
     * @param HttpClientInterface $datafordelerHttpClient
     *
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    public function getPersonData(string $cpr, HttpClientInterface $datafordelerHttpClient): array
    {
        $datafordelerService = new DatafordelerService($datafordelerHttpClient);

        try {
            $data = $datafordelerService->getPersonData($cpr);
        } catch (\Exception $e) {
            throw new \Exception('Failed to get person data', 1);
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
    protected function getBopaelssamling(array $cprAdresse, string $relationCpr, string $relationFullname, HttpClientInterface $datafordelerHttpClient): array
    {
        $query = ['adropl.status.eq' => 'aktuel'];
        foreach ($cprAdresse as $key => $value) {
            $query['cadr.'.$key.'.eq'] = $value;
        }
        $response = $datafordelerHttpClient->request(
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
    protected function getFullnameFromNameObject(array $navn): string
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

        if (404 === $response->getStatusCode()) {
            return [];
        }

        return $response->toArray();
    }

    /**
     * @Route("/getMorePNumbers", name="get_more_p_numbers")
     *
     * Fetches more P-Numbers with an option to limit the number of records fetched.
     *
     * @param Request              $request
     * @param HttpClientInterface $datafordelerHttpClient
     *
     * @return JsonResponse
     *
     * @throws TransportExceptionInterface
     */
    public function getMorePNumbers(Request $request, HttpClientInterface $datafordelerHttpClient): JsonResponse
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 5);
        $p_numbers = json_decode($request->query->get('p_numbers'), true);
    
        $allData = [];
        $datafordelerService = new DatafordelerService($datafordelerHttpClient);

        $p_numbers = array_slice($p_numbers, $offset, $limit);
        foreach ($p_numbers as $pnumber) {
            $data = $datafordelerService->getVirksomhedDataByPNumber($pnumber);
            $allData[$pnumber] = $data;
        }
    
        return new JsonResponse($allData);
    }
}
