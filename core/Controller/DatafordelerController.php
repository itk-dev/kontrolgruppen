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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/datafordeler")
 */
class DatafordelerController extends BaseController
{
    /**
     * @param string              $cpr
     * @param HttpClientInterface $datafordelerCprHttpClient
     *
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    public function getPersonData(string $cpr, HttpClientInterface $datafordelerCprHttpClient): array
    {
        $datafordelerService = new DatafordelerService($datafordelerCprHttpClient, null);

        try {
            $data = $datafordelerService->getPersonData($cpr);
        } catch (\Exception $e) {
            throw new \Exception('Failed to get person data', 1);
        }

        return $data;
    }

    /**
     * @Route("/getMorePNumbers", name="get_more_p_numbers")
     *
     * Fetches more P-Numbers with an option to limit the number of records fetched.
     *
     * @param Request             $request
     * @param HttpClientInterface $datafordelerCvrHttpClient
     *
     * @return JsonResponse
     *
     * @throws TransportExceptionInterface
     */
    public function getMorePNumbers(Request $request, HttpClientInterface $datafordelerCvrHttpClient): JsonResponse
    {
        $pNumbers = json_decode($request->query->get('p_numbers'), true);

        $allData = [];
        $datafordelerService = new DatafordelerService(null, $datafordelerCvrHttpClient);

        foreach ($pNumbers as $pnumber) {
            $data = $datafordelerService->getVirksomhedDataByPNumber($pnumber);
            $allData[$pnumber] = $data;
        }

        return new JsonResponse($allData);
    }

    /**
     * @param array               $cprAdresse
     * @param string              $relationCpr
     * @param string              $relationFullname
     * @param HttpClientInterface $datafordelerCprHttpClient
     *
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    protected function getBopaelssamling(array $cprAdresse, string $relationCpr, string $relationFullname, HttpClientInterface $datafordelerCprHttpClient): array
    {
        $query = ['adropl.status.eq' => 'aktuel'];
        foreach ($cprAdresse as $key => $value) {
            $query['cadr.'.$key.'.eq'] = $value;
        }
        $response = $datafordelerCprHttpClient->request(
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
     * @param HttpClientInterface $datafordelerCvrHttpClient
     *
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    protected function getVirksomhedData(string $cvr, HttpClientInterface $datafordelerCvrHttpClient): array
    {
        $response = $datafordelerCvrHttpClient->request(
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
}
