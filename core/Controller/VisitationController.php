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
use Kontrolgruppen\CoreBundle\Entity\VisitationLogEntry;
use Kontrolgruppen\CoreBundle\Repository\VisitationRepository;
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
     * @param Request $request
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
                ),
            ]
        );
    }

    /**
     * @Route("/search", name="visitation_search", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Kontrolgruppen\CoreBundle\CPR\CprException
     */
    public function search(Request $request): Response
    {
        // $visitation = new Visitation();
        // try {
        //     $client = $clientManager->createClient($request->get('clientType') ?? '');
        // } catch (\Exception $exception) {
        //     $this->addFlash('danger', $exception->getMessage());

        //     return $this->render('process/select-client-type.html.twig');
        // }

        // $visitation->setVisitationClient($client);
        // $form = $this->createForm(VisitationType::class, $visitation);

        // $form->handleRequest($request);
        return $this->render(
            '@KontrolgruppenCore/visitation/search.html.twig',
            [
                'client_type' => $request->get('clientType'),
                // 'menuItems' => $this->menuService->getProcessMenu(
                //     $request->getPathInfo(),
                // ),
                // 'visitation' => $visitation,
                // 'form' => $form->createView()
            ]
        );
        // if ($form->isSubmitted() && $form->isValid()) {
        //     // You can now redirect to your desired route
        //     return $this->render(
        //         '@KontrolgruppenCore/visitation/search.html.twig',
        //         [
        //             'menuItems' => $this->menuService->getProcessMenu(
        //                 $request->getPathInfo(),
        //             ),
        //             'visitation' => $visitation,
        //             'form' => $form->createView()
        //         ]
        //     );
        // }

        // return $this->render(
        //     '@KontrolgruppenCore/visitation/search.html.twig',
        //     [
        //         'menuItems' => $this->menuService->getProcessMenu(
        //             $request->getPathInfo(),
        //         ),
        //         'visitation' => $visitation,
        //         'form' => $form->createView()
        //     ]
        // );
    }

    /**
     * @Route("/result", name="result", methods={"GET","POST"})
     *
     * @param Request             $request
     * @param HttpClientInterface $datafordelerHttpClient
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Kontrolgruppen\CoreBundle\CPR\CprException
     */
    public function results(Request $request, HttpClientInterface $datafordelerHttpClient): Response
    {
        $cpr = $request->get('cpr');
        $cvr = $request->get('cvr');
        $visitation = new Visitation();
        if ($cvr) {
            $visitation->setType('company');
            $visitation->setIdentifier($cvr);
            $this->em->persist($visitation);
            $this->em->flush();

            try {
                $data = $this->getVirksomhedData($cvr, $datafordelerHttpClient);
                if (null === $data) {
                    return $this->render(
                        '@KontrolgruppenCore/visitation/search.html.twig',
                        [
                            'client_type' => 'company',
                            'error' => 'CVR nummer ikke genkendt, prøv igen.',
                        ]
                    );
                }
            } catch (TransportExceptionInterface $e) {
                return $this->render(
                    '@KontrolgruppenCore/visitation/search.html.twig',
                    [
                        'client_type' => 'company',
                        'error' => 'Forbindelse fejlet. Prøv igen',
                    ]
                );
            }
            if (!empty($data)) {
                // foreach $data['produktionsenheder']
                foreach ($data['produktionsenheder'] as $value) {
                    try {
                        // add to data['p-numre']
                        $data['p-numre'][] = $this->getVirksomhedDataByPNumber($value['pNummer'], $datafordelerHttpClient);
                    } catch (TransportExceptionInterface $e) {
                        return $this->render(
                            '@KontrolgruppenCore/visitation/search.html.twig',
                            [
                                'client_type' => 'company',
                                'error' => 'Forbindelse fejlet. Prøv igen',
                            ]
                        );
                    }
                }

                return $this->render(
                    '@KontrolgruppenCore/visitation/virksomhed_results.html.twig',
                    [
                        'data' => $data,
                        'visitation' => $visitation,
                    ]
                );
            }

            $this->addFlash('danger', 'Failed to fetch data from the API.');

            return $this->render(
                '@KontrolgruppenCore/visitation/virksomhed_results.html.twig'
            );
        } elseif ($cpr) {
            $visitation->setType('person');
            $hashedCpr = hash('md5', $cpr);
            $visitation->setIdentifier($hashedCpr);
            $this->em->persist($visitation);
            $this->em->flush();

            $cpr = preg_replace('/\D+/', '', $cpr);
            try {
                $data = $this->getPersonData($cpr, $datafordelerHttpClient);
                if (null === $data) {
                    return $this->render(
                        '@KontrolgruppenCore/visitation/search.html.twig',
                        [
                            'client_type' => 'person',
                            'error' => 'CPR nummer ikke genkendt, prøv igen.',
                        ]
                    );
                }
            } catch (TransportExceptionInterface $e) {
                return $this->render(
                    '@KontrolgruppenCore/visitation/search.html.twig',
                    [
                        'client_type' => 'person',
                        'error' => 'Forbindelse fejlet. Prøv igen',
                    ]
                );
            }
            if (!empty($data)) {
                return $this->render(
                    '@KontrolgruppenCore/visitation/person_results.html.twig',
                    [
                        'data' => $data,
                        'visitation' => $visitation,
                    ]
                );
            }

            $this->addFlash('danger', 'Failed to fetch data from the API.');

            return $this->render(
                '@KontrolgruppenCore/visitation/person_results.html.twig'
            );
        }
    }

    /**
     * @Route("/search-visitation-by-cvr", name="visitation_search_by_cpr", methods={"GET"})
     *
     * @param Request           $request
     * @param VisitationRepository $visitationRepository
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

    /**
     * @Route("/visitation-log", name="log_visitation", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function VisitationLog(Request $request): Response
    {
        $tableName = $request->request->get('table_name');
        $visitationId = $request->request->get('visitation');

        $visitationId = (int) $visitationId;
        $visitationLog = new VisitationLogEntry();

        // check if cpr or cvr
        if (null !== $request->request->get('cpr')) {
            $visitationLog->setTableName($tableName);
            $visitationLog->setVisitation($this->em->getRepository(Visitation::class)->find($visitationId));
        } elseif (null !== $request->request->get('cvr')) {
            $visitationLog->setTableName($tableName);
            $visitationLog->setVisitation($this->em->getRepository(Visitation::class)->find($visitationId));
        } else {
            return new Response(Response::HTTP_BAD_REQUEST);
        }
        $this->em->persist($visitationLog);
        $this->em->flush();

        // return 200
        return new Response(Response::HTTP_OK);
    }
}
