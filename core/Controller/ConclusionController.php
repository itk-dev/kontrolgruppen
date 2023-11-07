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
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;
use Kontrolgruppen\CoreBundle\Event\GetConclusionTemplateEvent;
use Kontrolgruppen\CoreBundle\Service\ConclusionService;
use Kontrolgruppen\CoreBundle\Service\DatafordelerService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process/{process}/conclusion")
 */
class ConclusionController extends BaseController
{
    /**
     * @Route("/", name="conclusion_show", methods={"GET","POST"})
     *
     * @param Request                  $request
     * @param Process                  $process
     * @param EventDispatcherInterface $dispatcher
     * @param DatafordelerService      $datafordelerService
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Process $process, EventDispatcherInterface $dispatcher, DatafordelerService $datafordelerService): Response
    {
        $conclusion = $process->getConclusion();

        // Create conclusion for the given process, if none exist.
        if (null === $conclusion) {
            $conclusionType = $process->getProcessType()->getConclusionClass();
            $conclusion = new $conclusionType();

            $this->em->persist($conclusion);

            $process->setConclusion($conclusion);

            $this->em->flush();
        }

        // Get template from event.
        $event = new GetConclusionTemplateEvent($conclusion::class, 'show');
        $template = $dispatcher->dispatch($event, GetConclusionTemplateEvent::NAME)->getTemplate();
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

        return $this->render($template, [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
            'conclusion' => $process->getConclusion(),
            'process' => $process,
            'data' => $data,
        ]);
    }

    /**
     * @Route("/edit", name="conclusion_edit", methods={"GET","POST"})
     *
     * @param Request                  $request
     * @param Process                  $process
     * @param ConclusionService        $conclusionService
     * @param EventDispatcherInterface $dispatcher
     * @param DatafordelerService      $datafordelerService
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, Process $process, ConclusionService $conclusionService, EventDispatcherInterface $dispatcher, DatafordelerService $datafordelerService): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $conclusion = $process->getConclusion();
        $options = [];

        // Disable the form if the process is completed.
        if (null !== $process->getCompletedAt()) {
            $options['disabled'] = true;
        }

        $form = $this->createForm($conclusionService->getEntityFormType($conclusion), $conclusion, $options);

        // Only handle form if the process is not completed.
        if (null === $process->getCompletedAt()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->flush();

                return $this->redirectToRoute('conclusion_show', [
                    'process' => $process->getId(),
                ]);
            }
        }

        // Get template from event.
        $event = new GetConclusionTemplateEvent($conclusion::class, 'edit');
        $template = $dispatcher->dispatch($event, GetConclusionTemplateEvent::NAME)->getTemplate();
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

        return $this->render($template, [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'conclusion' => $conclusion,
            'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
            'form' => $form->createView(),
            'process' => $process,
            'data' => $data,
        ]);
    }
}
