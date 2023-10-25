<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Service\EconomyService;
use Symfony\Component\HttpFoundation\Request;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;
use Kontrolgruppen\CoreBundle\Service\DatafordelerService;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EconomyController.
 *
 * @Route("/process/{process}/revenue")
 */
class RevenueController extends BaseController
{
    /**
     * @Route("/", name="economy_revenue")
     *
     * @param Request        $request
     * @param Process               $process
     * @param EconomyService        $economyService
     * @param DatafordelerService   $datafordelerService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function revenue(Request $request, Process $process, EconomyService $economyService, DatafordelerService $datafordelerService)
    {
        $parameters = [];

        $parameters['revenue'] = $economyService->calculateRevenue($process);

        $parameters['menuItems'] = $this->menuService->getProcessMenu($request->getPathInfo(), $process);
        $parameters['process'] = $process;

        // Get the ProcessClient Identifier from process
        $processClientIdentifier = $process->getProcessClient()->getIdentifier();
        // Get client type
        $clientType = $process->getProcessClient()->getType();

        if (ProcessClientPerson::PERSON === $clientType) {
            $processClientIdentifier = preg_replace('/\D+/', '', $processClientIdentifier);
            $data = $datafordelerService->getPersonData($processClientIdentifier);
        } elseif (ProcessClientCompany::COMPANY === $clientType) {
            $data = $datafordelerService->getVirksomhedData($processClientIdentifier);
        }

        $parameters['data'] = $data;


        return $this->render(
            '@KontrolgruppenCore/revenue/revenue.html.twig',
            $parameters
        );
    }
}
