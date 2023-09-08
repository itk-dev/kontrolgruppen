<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Visitation;
use Kontrolgruppen\CoreBundle\Form\VisitationType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Service\ProcessClientManager;
use Mpdf\Container\NotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/visitation")
 */
class VisitationController extends DatafordelerController
{
    /**
     * @Route("/", name="visitation_index", methods={"GET"})
     *
     * @param Request                       $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request): Response
    {
        return $this->render(
            '@KontrolgruppenCore/visitation/index.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo()
                )
            ]
        );
    }

    /**
     * @Route("/investigate", name="visitation_investigate", methods={"GET","POST"})
     *
     * @param Request              $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Kontrolgruppen\CoreBundle\CPR\CprException
     */
    public function investigate(Request $request, ProcessClientManager $clientManager): Response
    {
        $visitation = new Visitation();
        try {
            $client = $clientManager->createClient($request->get('clientType') ?? '');
        } catch (\Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());

            return $this->render('process/select-client-type.html.twig');
        }

        $visitation->setVisitationClient($client);
        $form = $this->createForm(VisitationType::class, $visitation);

        $form->handleRequest($request);

        // Check if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // You can now redirect to your desired route
            return $this->redirectToRoute('result', [
                'cpr' => $form->get('cpr')->getData(),  // Assuming 'cpr' is the form field name
            ]);
        }

        return $this->render(
            '@KontrolgruppenCore/visitation/investigate.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                ),
                'visitation' => $visitation,
                'form' => $form->createView()
            ]
        );
    }


    /**
     * @Route("/result", name="result", methods={"GET","POST"})
     *
     * @param Request              $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Kontrolgruppen\CoreBundle\CPR\CprException
     */
    public function results(Request $request, ProcessClientManager $clientManager, HttpClientInterface $httpClient): Response
    {
        $cpr = $request->get('cpr');

        if (!$cpr) {
            throw new NotFoundHttpException('No CPR found!');
        }

        $cpr = preg_replace('/\D+/', '', $cpr);

        try {
            $data = $this->getPersonData($cpr, $httpClient);
        } catch (TransportExceptionInterface $e) {
            throw new NotFoundException($e->getMessage());
        }

        if (!empty($data)) {
            return $this->render(
                '@KontrolgruppenCore/visitation/results.html.twig',
                [
                    'data' => $data
                ]
            );
        } else {
            $this->addFlash('danger', 'Failed to fetch data from the API.');

            return $this->render(
                '@KontrolgruppenCore/visitation/results.html.twig'
            );
        }
    }

    /**
     * @Route("/search-visitation-by-cvr", name="visitation_search_by_cpr", methods={"GET"})
     *
     * @param Request           $request
     * @param ProcessRepository $processRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function searchProcessesByCvr(Request $request): Response
    {
        $cvr = $request->get('cvr');
        if (!$cvr) {
            throw new NotFoundHttpException('No CVR found!');
        }

        return $this->render(
            '@KontrolgruppenCore/visitation/_visitation_search_cvr_result.html.twig'
        );
    }

}
