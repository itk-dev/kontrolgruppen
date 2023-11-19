<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Kontrolgruppen\CoreBundle\CPR\CprException;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\Entity\AbstractProcessClient;
use Kontrolgruppen\CoreBundle\Entity\Car;
use Kontrolgruppen\CoreBundle\Entity\ContactPerson;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;
use Kontrolgruppen\CoreBundle\Form\ProcessClientCompanyType;
use Kontrolgruppen\CoreBundle\Form\ProcessClientPersonType;
use Kontrolgruppen\CoreBundle\Service\DatafordelerService;
use Kontrolgruppen\CoreBundle\Service\MenuService;
use Kontrolgruppen\CoreBundle\Service\ProcessClientManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/process/{process}/client")
 */
class ProcessClientController extends BaseController
{
    /**
     * @var ProcessClientManager
     */
    private $processClientManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ProcessClientController constructor.
     *
     * @param RequestStack           $requestStack
     * @param MenuService            $menuService
     * @param CprServiceInterface    $processClientManager
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $em
     */
    public function __construct(RequestStack $requestStack, MenuService $menuService, ProcessClientManager $processClientManager, LoggerInterface $logger, EntityManagerInterface $em)
    {
        parent::__construct($requestStack, $menuService, $em);
        $this->processClientManager = $processClientManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="client_show", methods={"GET","POST"})
     *
     * @param Request             $request
     * @param Process             $process
     * @param DatafordelerService $datafordelerService
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Process $process, DatafordelerService $datafordelerService): Response
    {
        $client = $process->getProcessClient();

        if (null === $client) {
            throw new \RuntimeException('@todo create client');
        }

        $changeProcessStatusForm = $this->createChangeProcessStatusForm($process);
        $this->handleChangeProcessStatusForm($request, $changeProcessStatusForm);

        $newInfoAvailable = $this->isGranted('edit', $process) && $this->isNewClientInfoAvailable($client);

        $view = $this->getView($client, 'show');

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

        return $this->render($view, [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'client' => $client,
            'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
            'changeProcessStatusForm' => $changeProcessStatusForm->createView(),
            'data' => $data,
            'process' => $process,
            'newClientInfoAvailable' => $newInfoAvailable,
        ]);
    }

    /**
     * @Route("/edit", name="client_edit", methods={"GET","POST"})
     *
     * @param Request             $request
     * @param Process             $process
     * @param DatafordelerService $datafordelerService
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, Process $process, DatafordelerService $datafordelerService): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        // Redirect to show if process is completed.
        if (null !== $process->getCompletedAt()) {
            return $this->redirectToRoute('client_show', [
                'process' => $process->getId(),
            ]);
        }

        $client = $process->getProcessClient();

        $form = $this->createClientForm($client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('client_show', [
                'process' => $process->getId(),
            ]);
        }

        $view = $this->getView($client, 'edit');

        // Get the ProcessClient Identifier from process
        $processClientIdentifier = $client->getIdentifier();
        // Get client type
        $clientType = $client->getType();

        if (ProcessClientPerson::PERSON === $clientType) {
            $processClientIdentifier = preg_replace('/\D+/', '', $processClientIdentifier);
            $data = $datafordelerService->getPersonData($processClientIdentifier);
        } elseif (ProcessClientCompany::COMPANY === $clientType) {
            $data = $datafordelerService->getVirksomhedData($processClientIdentifier);
        }

        return $this->render($view, [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
            'client' => $client,
            'form' => $form->createView(),
            'data' => $data,
            'process' => $process,
        ]);
    }

    /**
     * @Route("/update", name="client_update", methods={"GET"})
     *
     * @param Request             $request
     * @param Process             $process
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function update(Request $request, Process $process, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $client = $process->getProcessClient();

        try {
            $client = $this->processClientManager->populateClient($client);
            $this->addFlash('success', $translator->trans('client.show.client_updated'));
        } catch (CprException $e) {
            $this->addFlash('danger', $translator->trans('client.show.client_not_updated'));
            $this->logger->error($e);
        }

        $this->em->persist($client);
        $this->em->flush();

        return $this->redirectToRoute('client_show', ['process' => $process]);
    }

    /**
     * @Route("/update_client", name="client_update_info", methods={"POST"})
     *
     * @param Request $request
     * @param Process $process
     *
     * @return Response
     */
    public function updateClient(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        // Redirect to show if process is completed.
        if (null !== $process->getCompletedAt()) {
            return $this->redirectToRoute('client_show', [
                'process' => $process->getId(),
            ]);
        }

        $client = $process->getProcessClient();

        // Get the ProcessClient Identifier from process
        $processClientIdentifier = $client->getIdentifier();
        // Get client type
        $clientType = $client->getType();

        if (ProcessClientPerson::PERSON === $clientType) {
            $client->setTelephone($request->get('telephone'));
            $car = new Car();
            $car->setRegistrationNumber($request->get('registrationNumber'));
            $client->addCar($car);
            $this->em->flush();
            $this->em->persist($client);
            $this->em->flush();
        } elseif (ProcessClientCompany::COMPANY === $clientType) {
            $contactPerson = new ContactPerson();
            $contactPerson->setName($request->get('contactPersonName'));
            $contactPerson->setTelephone($request->get('contactPersonPhone'));
            $client->setContactPerson($contactPerson);
            $client->setPNumber($request->get('pNumber'));
            $this->em->flush();
            $this->em->persist($client);
            $this->em->flush();
        }

        return $this->redirectToRoute('client_show', [
            'process' => $process->getId(),
        ]);
    }

    /**
     * Check if new info is available for a client.
     *
     * @param AbstractProcessClient $client
     *
     * @return bool True if new info is available
     */
    private function isNewClientInfoAvailable(AbstractProcessClient $client): bool
    {
        try {
            return $this->processClientManager->isNewClientInfoAvailable($client);
        } catch (CprException $e) {
            $this->logger->error($e);
        }

        return false;
    }

    /**
     * Get view for a client action.
     *
     * @param AbstractProcessClient $client
     * @param string                $action
     *
     * @return string The view
     */
    private function getView(AbstractProcessClient $client, string $action): string
    {
        return '@KontrolgruppenCore/client/'.$client->getType().'/'.$action.'.html.twig';
    }

    /**
     * Create client form.
     *
     * @param AbstractProcessClient $client
     *
     * @return FormInterface The client form
     */
    private function createClientForm(AbstractProcessClient $client): FormInterface
    {
        if ($client instanceof ProcessClientCompany) {
            return $this->createForm(ProcessClientCompanyType::class, $client);
        }
        if ($client instanceof ProcessClientPerson) {
            return $this->createForm(ProcessClientPersonType::class, $client);
        }

        throw new RuntimeException(sprintf('Unknown client type: %s', $client::class));
    }
}
