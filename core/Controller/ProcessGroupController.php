<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessGroup;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;
use Kontrolgruppen\CoreBundle\Form\ProcessGroupType;
use Kontrolgruppen\CoreBundle\Service\DatafordelerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process/{process}/group")
 */
class ProcessGroupController extends BaseController
{
    /**
     * @Route("/", name="process_group_index", methods={"GET"})
     *
     * @param Request               $request
     * @param Process               $process
     * @param DatafordelerService   $process
     *
     * @return Response
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function index(Request $request, Process $process, DatafordelerService $datafordelerService): Response
    {
        $processGroups = [];

        foreach ($process->getProcessGroups() as $processGroup) {
            $processGroups[] = [
                'processGroup' => $processGroup,
                'processIterator' => $this->getSortedByPrimaryIterator(
                    $processGroup->getPrimaryProcess(),
                    $processGroup->getProcesses()->getIterator()
                ),
            ];
        }
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

        return $this->render('@KontrolgruppenCore/process_group/index.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu(
                $request->getPathInfo(),
                $process
            ),
            'process' => $process,
            'data' => $data,
            'process_groups' => $processGroups,
        ]);
    }

    /**
     * @Route("/new", name="process_group_new", methods={"GET","POST"})
     *
     * @param Request               $request
     * @param Process               $process
     * @param DatafordelerService   $process

     *
     * @return Response
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function new(Request $request, Process $process, DatafordelerService $datafordelerService): Response
    {
        $processGroup = new ProcessGroup();
        $processGroup->setPrimaryProcess($process);
        $processGroup->addProcess($process);
        $form = $this->createForm(ProcessGroupType::class, $processGroup);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // We filter out the primary process from the select, so we have to add it manually.
            $processGroup->addProcess($processGroup->getPrimaryProcess());
            $entityManager = $this->em;
            $entityManager->persist($processGroup);
            $entityManager->flush();

            return $this->redirectToRoute('process_group_index', ['process' => $process]);
        }

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

        return $this->render('@KontrolgruppenCore/process_group/new.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu(
                $request->getPathInfo(),
                $process
            ),
            'process_group' => $processGroup,
            'process' => $process,
            'data' => $data,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="process_group_edit", methods={"GET","POST"})
     *
     * @param Request               $request
     * @param ProcessGroup          $processGroup
     * @param Process               $process
     * @param DatafordelerService   $process
     *
     * @return Response
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function edit(Request $request, ProcessGroup $processGroup, Process $process, DatafordelerService $datafordelerService): Response
    {
        $form = $this->createForm(ProcessGroupType::class, $processGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // We filter out the primary process from the select, so we have to add it manually.
            $processGroup->addProcess($processGroup->getPrimaryProcess());
            $this->em->flush();

            return $this->redirectToRoute('process_group_index', ['process' => $process]);
        }
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
        return $this->render('@KontrolgruppenCore/process_group/edit.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu(
                $request->getPathInfo(),
                $process
            ),
            'process_group' => $processGroup,
            'form' => $form->createView(),
            'process' => $process,
            'data' => $data,
        ]);
    }

    /**
     * @Route("/{id}", name="process_group_delete", methods={"DELETE"})
     *
     * @param Request               $request
     * @param ProcessGroup          $processGroup
     * @param Process               $process
     *
     * @return Response
     */
    public function delete(Request $request, ProcessGroup $processGroup, Process $process): Response
    {
        if ($this->isCsrfTokenValid('delete'.$processGroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->em;
            $entityManager->remove($processGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('process_group_index', ['process' => $process]);
    }

    /**
     * @param Process $primaryProcess
     * @param $processIterator
     *
     * @return mixed
     */
    private function getSortedByPrimaryIterator(Process $primaryProcess, $processIterator)
    {
        $processIterator->uasort(function (Process $first, Process $second) use ($primaryProcess) {
            if ($first->getId() === $primaryProcess->getId()) {
                return -1;
            }

            if ($second->getId() === $primaryProcess->getId()) {
                return 1;
            }

            $firstCaseNumber = (int) str_replace('-', '', $first->getCaseNumber());
            $secondCaseNumber = (int) str_replace('-', '', $second->getCaseNumber());

            return $firstCaseNumber <=> $secondCaseNumber;
        });

        return $processIterator;
    }
}
