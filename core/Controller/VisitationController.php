<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kontrolgruppen\CoreBundle\Entity\Visitation;
use Kontrolgruppen\CoreBundle\Entity\VisitationClientPerson;
use Kontrolgruppen\CoreBundle\Entity\VisitationLogEntry;
use Kontrolgruppen\CoreBundle\Form\VisitationType;
use Kontrolgruppen\CoreBundle\Repository\VisitationRepository;
use Kontrolgruppen\CoreBundle\Service\VisitationClientManager;
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
    public function investigate(Request $request): Response
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
            '@KontrolgruppenCore/visitation/investigate.html.twig',
            [
                'client_type' =>$request->get('clientType')
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
        //         '@KontrolgruppenCore/visitation/investigate.html.twig',
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
        //     '@KontrolgruppenCore/visitation/investigate.html.twig',
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
     * @param Request              $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Kontrolgruppen\CoreBundle\CPR\CprException
     */
    public function results(Request $request, HttpClientInterface $httpClient): Response
    {
        $cpr = $request->get('cpr');
        $cvr = $request->get('cvr');
        $visitation = new Visitation();
        if ($cvr) {
            $visitation->setType('virksomhed');

            try {
                $data = $this->getVirksomhedData($cvr, $httpClient);
            } catch (TransportExceptionInterface $e) {
                throw new NotFoundException($e->getMessage());
            }
            if (!empty($data)) {
                return $this->render(
                    '@KontrolgruppenCore/visitation/virksomhed_results.html.twig',
                    [
                        'data' => $data
                    ]
                );
            } else {
                $this->addFlash('danger', 'Failed to fetch data from the API.');
    
                return $this->render(
                    '@KontrolgruppenCore/visitation/virksomhed_results.html.twig'
                );
            }
        }
        else if($cpr) {
            $visitation->setType('person');
            $hashedCpr = hash ('md5' ,$cpr);
            $visitation->setIdentifier($hashedCpr);
            $this->em->persist($visitation);
            $this->em->flush();

            $cpr = preg_replace('/\D+/', '', $cpr);
            try {
                $data = $this->getPersonData($cpr, $httpClient);
            } catch (TransportExceptionInterface $e) {
                throw new NotFoundException($e->getMessage());
            }
            if (!empty($data)) {
                return $this->render(
                    '@KontrolgruppenCore/visitation/person_results.html.twig',
                    [
                        'data' => $data,
                        'visitation' => $visitation
                    ]
                );
            } else {
                $this->addFlash('danger', 'Failed to fetch data from the API.');
    
                return $this->render(
                    '@KontrolgruppenCore/visitation/person_results.html.twig'
                );
            }
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
     * @param Request           $request
     * @param VisitationRepository $visitationRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function VisitationLog(Request $request): Response
    {
        $table_name = $request->request->get('table_name');
        $cpr = $request->request->get('cpr');
        $visitation_id = $request->request->get('visitation');
        
        $visitation_id = (int)$visitation_id;
        // find visitation by id

        $visitationLog = new VisitationLogEntry();
        $hashedCpr = hash ('md5' ,$cpr);
        $visitationLog->setCprNumber($hashedCpr);
        $visitationLog->setTableName($table_name);
        $visitationLog->setVisitation($this->em->getRepository(Visitation::class)->find($visitation_id));
        // $visitation->setVisitationClient(VisitationClientPerson);
        // $visitation->setCompletedAt(null);
        // $visitation->setLockedNetValue(null);
        // $visitation->setLastReopened(new \DateTime());
        // $em = $this->em->getManager();
        $this->em->persist($visitationLog);
        $this->em->flush();

        // return 200
        return new Response(Response::HTTP_OK);
    }

}
