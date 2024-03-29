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
use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\DBAL\Types\ProcessLogEntryLevelEnumType;
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Kontrolgruppen\CoreBundle\Entity\LockedNetValue;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Entity\ProcessType as ProcessTypeEntity;
use Kontrolgruppen\CoreBundle\Filter\ProcessFilterType;
use Kontrolgruppen\CoreBundle\Form\ProcessCompleteType;
use Kontrolgruppen\CoreBundle\Form\ProcessResumeType;
use Kontrolgruppen\CoreBundle\Form\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\AbstractTaxonomyRepository;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Kontrolgruppen\CoreBundle\Repository\UserRepository;
use Kontrolgruppen\CoreBundle\Service\DatafordelerService;
use Kontrolgruppen\CoreBundle\Service\LogManager;
use Kontrolgruppen\CoreBundle\Service\ProcessClientManager;
use Kontrolgruppen\CoreBundle\Service\ProcessManager;
use Kontrolgruppen\CoreBundle\Service\UserSettingsService;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/process")
 */
class ProcessController extends BaseController
{
    /**
     * @Route("/", name="process_index", methods={"GET"})
     *
     * @param Request                       $request
     * @param ProcessRepository             $processRepository
     * @param FilterBuilderUpdaterInterface $lexikBuilderUpdater
     * @param PaginatorInterface            $paginator
     * @param FormFactoryInterface          $formFactory
     * @param ProcessManager                $processManager
     * @param UserRepository                $userRepository
     * @param UserSettingsService           $userSettingsService
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, ProcessRepository $processRepository, FilterBuilderUpdaterInterface $lexikBuilderUpdater, PaginatorInterface $paginator, FormFactoryInterface $formFactory, ProcessManager $processManager, UserRepository $userRepository, UserSettingsService $userSettingsService): Response
    {
        $filterForm = $formFactory->create(ProcessFilterType::class);

        $queryBuilder = null;

        $selectedCaseWorker = $filterForm->get('caseWorker');
        if (null === $selectedCaseWorker->getData()) {
            $filterForm->get('caseWorker')->setData($this->getUser()->getId());
        }

        if ($request->query->has($filterForm->getName())) {
            $formParameters = $request->get($filterForm->getName());

            if (!isset($formParameters['caseWorker'])) {
                $formParameters['caseWorker'] = $this->getUser()->getId();
            }

            // manually bind values from the request
            $filterForm->submit($formParameters);

            // initialize a query builder
            $queryBuilder = $processRepository->createQueryBuilder('e');

            // build the query from the given form object
            $lexikBuilderUpdater->addFilterConditions($filterForm, $queryBuilder);
        } else {
            $queryBuilder = $processRepository->createQueryBuilder('e');

            $queryBuilder->where('e.caseWorker = :caseWorker');
            $queryBuilder->setParameter(':caseWorker', $this->getUser());
        }

        // Add sortable fields.
        $queryBuilder->leftJoin('e.processClient', 'client');
        $queryBuilder->addSelect('partial client.{id,identifier,name,type}');

        $queryBuilder->leftJoin('e.caseWorker', 'caseWorker');
        $queryBuilder->addSelect('partial caseWorker.{id}');

        $queryBuilder->leftJoin('e.channel', 'channel');
        $queryBuilder->addSelect('partial channel.{id,name}');

        $queryBuilder->leftJoin('e.reason', 'reason');
        $queryBuilder->addSelect('partial reason.{id,name}');

        $queryBuilder->leftJoin('e.service', 'service');
        $queryBuilder->addSelect('partial service.{id,name}');

        $queryBuilder->leftJoin('e.processType', 'processType');
        $queryBuilder->addSelect('partial processType.{id}');

        $queryBuilder->leftJoin('e.processStatus', 'processStatus');
        $queryBuilder->addSelect('partial processStatus.{id}');

        $formKey = 'process_index.'.$filterForm->getName();
        /* @var \Kontrolgruppen\CoreBundle\Entity\User $user */
        $user = $this->getUser();

        $paginatorOptions = [
            'defaultSortFieldName' => 'e.caseNumber',
            'defaultSortDirection' => 'desc',
        ];

        // Get sort and direction from user settings.
        if (!$request->query->has('sort') && !$request->query->has('direction')) {
            $userSettings = $userSettingsService->getSettings($user, $formKey);

            if ($userSettings && null !== $userSettings->getSettingsValue()) {
                $userSettingsValue = $userSettings->getSettingsValue();
                $paginatorOptions = [
                    'defaultSortFieldName' => $userSettingsValue['sort'],
                    'defaultSortDirection' => $userSettingsValue['direction'],
                ];
            }
        } else {
            $userSettingsService->setSettings($user, $formKey, [
                'sort' => $request->query->get('sort'),
                'direction' => $request->query->get('direction'),
            ]);
        }

        $query = $queryBuilder->getQuery();

        // Get paginated result.
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            50,
            $paginatorOptions
        );
        // Find Processes that have not been visited by the assigned CaseWorker.
        $caseWorker = (!empty($selectedCaseWorker->getData())) ? $userRepository->find($selectedCaseWorker->getData()) : $this->getUser();
        $foundEntries = array_column($query->getArrayResult(), 'id');
        $notVisitedProcessIds = $processManager->getUsersUnvisitedProcessIds($foundEntries, $caseWorker);

        return $this->render(
            '@KontrolgruppenCore/process/index.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo()
                ),
                'unvisitedProcessIds' => $notVisitedProcessIds,
                'pagination' => $pagination,
                'form' => $filterForm->createView(),
                'query' => $query,
            ]
        );
    }

    /**
     * @Route("/new", name="process_new", methods={"GET","POST"})
     *
     * @param Request              $request
     * @param ProcessManager       $processManager
     * @param ProcessClientManager $clientManager
     * @param UserRepository       $userRepository
     * @param DatafordelerService  $datafordelerService
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Kontrolgruppen\CoreBundle\CPR\CprException
     */
    public function new(Request $request, ProcessManager $processManager, ProcessClientManager $clientManager, UserRepository $userRepository, DatafordelerService $datafordelerService): Response
    {
        $session = $request->getSession();
        $process = new Process();
        $this->denyAccessUnlessGranted('edit', $process);

        // Force user to select process client type before anything else.
        try {
            $client = $clientManager->createClient($request->get('clientType') ?? '');
            // Force user to select process client type before anything else.
        } catch (\Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }
        $caseWorker = $request->get('case_worker');
        $identifier = $request->get('identifier');
        if (null !== $identifier) {
            $session->set('identifier', $identifier);
        }

        if ($session->has('identifier') && null !== $session->get('identifier')) {
            $identifier = $session->get('identifier');
        } else {
            $identifier = null;
        }

        $caseWorker = $userRepository->findOneBy(['username' => $caseWorker]);
        $process->setProcessClient($client);
        $process->setCaseWorker($caseWorker);
        $pNumbers = [];
        if (ProcessClientCompany::COMPANY === $request->get('clientType')) {
            $virksomhedData = $datafordelerService->getVirksomhedData($identifier);
            // Extracting pNumbers from the response
            if (!empty($virksomhedData['produktionsenheder'])) {
                foreach ($virksomhedData['produktionsenheder'] as $produktionsenhed) {
                    $pNumbers[] = $produktionsenhed['pNummer'];
                }
            }
        }
        $session->set('pNumbers', $pNumbers);
        if ($session->has('pNumbers')) {
            $pNumbers = $session->get('pNumbers');
        } else {
            $pNumbers = [];
        }
        $form = $this->createForm(ProcessType::class, $process, [
            // Add the `personnummer` option to the form.
            'identifier' => $identifier,
            'pNumbers' => $pNumbers,
        ]);

        $this->handleTaxonomyCallback($form, $request, $process);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get unmapped client data from request.
            $data = $request->get('process');
            $clientType = $process->getProcessClient()->getType();
            $clientData = $data[$clientType] ?? [];
            $processCreated = false;
            try {
                $client = $clientManager->createClient($clientType, $clientData);
                // Populate the client to catch errors in (or missing) client data.
                $clientManager->populateClient($client);
                $process = $processManager->newProcess($process, $client);
                // Get the ProcessClient Identifier from process
                $processClientIdentifier = $process->getProcessClient()->getIdentifier();
                // Get client type
                $clientType = $process->getProcessClient()->getType();
                if (ProcessClientPerson::PERSON === $clientType) {
                    $processClientIdentifier = preg_replace('/\D+/', '', $processClientIdentifier);
                    $dataFordelerData = $datafordelerService->getPersonData($processClientIdentifier);
                    $client->setName($dataFordelerData['stamdata']['navn'] ?? null);
                    $this->em->persist($client);
                    $this->em->flush();
                } elseif (ProcessClientCompany::COMPANY === $clientType) {
                    $dataFordelerData = $datafordelerService->getVirksomhedData($processClientIdentifier);
                    $client->setAddress($dataFordelerData['beliggenhedsadresse']['CVRAdresse_vejnavn'].' '.$dataFordelerData['beliggenhedsadresse']['CVRAdresse_husnummerFra'] ?? null);
                    $client->setPostalCode($dataFordelerData['beliggenhedsadresse']['CVRAdresse_postnummer'] ?? null);
                    $client->setCity($dataFordelerData['beliggenhedsadresse']['CVRAdresse_postdistrikt'] ?? null);
                    $client->setName($dataFordelerData['virksomhedsnavn']['vaerdi'] ?? null);

                    // SAVE DATA
                    $this->em->persist($client);
                    $this->em->flush();
                    // remove identifier and pNumbers from session
                    $session->remove('identifier');
                    $session->remove('pNumbers');
                }

                $processCreated = true;
            } catch (\Exception $exception) {
                $identifierName = ProcessClientPerson::TYPE === $clientType ? 'cpr' : 'cvr';
                if ($form->has($clientType) && $form->get($clientType)->has($identifierName)) {
                    $form->get($clientType)->get($identifierName)->addError(new FormError($exception->getMessage()));
                } else {
                    $this->addFlash('danger', $exception->getMessage());
                }
            }

            if ($processCreated) {
                return $this->redirectToRoute('process_edit', ['id' => $process]);
            }
        }

        // Get latest log entries
        $recentActivity = $this->em->getRepository(
            ProcessLogEntry::class
        )->getLatestEntriesByLevel(ProcessLogEntryLevelEnumType::NOTICE, 10);

        return $this->render(
            '@KontrolgruppenCore/process/new.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'form' => $form->createView(),
                'recentActivity' => $recentActivity,
            ]
        );
    }

    /**
     * @Route("/search-process-by-cpr", name="process_search_by_cpr", methods={"GET"})
     *
     * @param Request           $request
     * @param ProcessRepository $processRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function searchProcessesByCpr(Request $request, ProcessRepository $processRepository): Response
    {
        $cpr = $request->get('cpr');
        if (!$cpr) {
            throw new NotFoundHttpException('No CPR found!');
        }

        $processes = $processRepository->findByClientIdentifier(ProcessClientPerson::TYPE, $cpr);

        return $this->render(
            '@KontrolgruppenCore/process/_process_search_cpr_result.html.twig',
            ['processes' => $processes]
        );
    }

    /**
     * @Route("/search-process-by-cvr", name="process_search_by_cvr", methods={"GET"})
     *
     * @param Request           $request
     * @param ProcessRepository $processRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function searchProcessesByCvr(Request $request, ProcessRepository $processRepository): Response
    {
        $cvr = $request->get('cvr');
        if (!$cvr) {
            throw new NotFoundHttpException('No CVR found!');
        }

        $processes = $processRepository->findByClientIdentifier(ProcessClientCompany::TYPE, $cvr);

        return $this->render(
            '@KontrolgruppenCore/process/_process_search_cvr_result.html.twig',
            ['processes' => $processes]
        );
    }

    /**
     * @Route("/{id}", name="process_show", methods={"GET", "POST"})
     *
     * @param Request             $request
     * @param Process             $process
     * @param LogManager          $logManager
     * @param DatafordelerService $datafordelerService
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Process $process, LogManager $logManager, DatafordelerService $datafordelerService): Response
    {
        // Latest journal entries.
        $latestJournalEntries = $this->em->getRepository(
            JournalEntry::class
        )->getLatestEntries($process);
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
        if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
            $latestJournalEntries = $logManager->attachLogEntriesToJournalEntries($latestJournalEntries);
        }

        return $this->render(
            '@KontrolgruppenCore/process/show.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'data' => $data,
                'latestJournalEntries' => $latestJournalEntries,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="process_edit", methods={"GET","POST"})
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

        if (null !== $process->getCompletedAt() && !$this->isGranted('ROLE_ADMIN')) {
            $this->redirectToRoute('process_show', ['id' => $process->getId()]);
        }

        $form = $this->createForm(ProcessType::class, $process);
        $this->handleTaxonomyCallback($form, $request, $process);
        $form->handleRequest($request);

        $processClientIdentifier = $process->getProcessClient()->getIdentifier();
        // Get client type
        $clientType = $process->getProcessClient()->getType();
        $client = $process->getProcessClient();

        if (ProcessClientPerson::PERSON === $clientType) {
            $processClientIdentifier = preg_replace('/\D+/', '', $processClientIdentifier);
            $data = $datafordelerService->getPersonData($processClientIdentifier);
            $client->setName($data['stamdata']['navn'] ?? null);
        } elseif (ProcessClientCompany::COMPANY === $clientType) {
            $data = $datafordelerService->getVirksomhedData($processClientIdentifier);
            $client->setName($data['virksomhedsnavn']['vaerdi'] ?? null);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToReferer(
                'process_show',
                [
                    'id' => $process->getId(),
                ]
            );
        }

        return $this->render(
            '@KontrolgruppenCore/process/edit.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'data' => $data,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="process_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Process $process
     *
     * @return Response
     */
    public function delete(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('delete', $process);

        if ($this->isCsrfTokenValid(
            'delete'.$process->getId(),
            $request->request->get('_token')
        )) {
            $entityManager = $this->em;
            $entityManager->remove($process);
            $entityManager->flush();
        }

        return $this->redirectToRoute('process_index');
    }

    /**
     * @Route("/{id}/complete", name="process_complete", methods={"GET","POST"})
     *
     * @param Request                 $request
     * @param Process                 $process
     * @param ServiceRepository       $serviceRepository
     * @param ProcessStatusRepository $processStatusRepository
     * @param ProcessManager          $processManager
     * @param DatafordelerService     $datafordelerService
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function complete(Request $request, Process $process, ServiceRepository $serviceRepository, ProcessStatusRepository $processStatusRepository, ProcessManager $processManager, DatafordelerService $datafordelerService): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        if (null !== $process->getCompletedAt()) {
            return $this->redirectToRoute(
                'process_show',
                ['id' => $process->getId()]
            );
        }

        $availableStatuses = $processStatusRepository->getAvailableCompletingStatusForProcessType($process->getProcessType());

        $form = $this->createForm(ProcessCompleteType::class, $process, [
            'available_statuses' => $availableStatuses,
        ]);
        $form->handleRequest($request);
        $processClientIdentifier = $process->getProcessClient()->getIdentifier();
        // Get client type
        $clientType = $process->getProcessClient()->getType();

        if (ProcessClientPerson::PERSON === $clientType) {
            $processClientIdentifier = preg_replace('/\D+/', '', $processClientIdentifier);
            $data = $datafordelerService->getPersonData($processClientIdentifier);
        } elseif (ProcessClientCompany::COMPANY === $clientType) {
            $data = $datafordelerService->getVirksomhedData($processClientIdentifier);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $services = $serviceRepository->getByProcess($process);
            foreach ($services as $service) {
                $lockedNetValue = new LockedNetValue();
                $lockedNetValue->setService($service);
                $lockedNetValue->setProcess($process);
                $lockedNetValue->setValue($service->getNetDefaultValue());

                $em->persist($lockedNetValue);
            }

            $em->flush();

            $processManager->completeProcess($process);

            return $this->redirectToRoute(
                'process_show',
                [
                    'id' => $process->getId(),
                ]
            );
        }

        return $this->render(
            '@KontrolgruppenCore/process/complete.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'form' => $form->createView(),
                'data' => $data,
            ]
        );
    }

    /**
     * @Route("/{id}/resume", name="process_resume", methods={"POST", "GET"})
     *
     * @param Request                 $request
     * @param Process                 $process
     * @param ProcessStatusRepository $processStatusRepository
     * @param DatafordelerService     $datafordelerService
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function resume(Request $request, Process $process, ProcessStatusRepository $processStatusRepository, DatafordelerService $datafordelerService): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $form = $this->createForm(ProcessResumeType::class, $process, [
            'available_statuses' => $processStatusRepository->getAvailableForProcess($process),
        ]);

        $form->handleRequest($request);
        $processClientIdentifier = $process->getProcessClient()->getIdentifier();
        // Get client type
        $clientType = $process->getProcessClient()->getType();

        if (ProcessClientPerson::PERSON === $clientType) {
            $processClientIdentifier = preg_replace('/\D+/', '', $processClientIdentifier);
            $data = $datafordelerService->getPersonData($processClientIdentifier);
        } elseif (ProcessClientCompany::COMPANY === $clientType) {
            $data = $datafordelerService->getVirksomhedData($processClientIdentifier);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $process->setCompletedAt(null);
            $process->setLockedNetValue(null);
            $process->setLastReopened(new \DateTime());
            $em = $this->em;
            $em->persist($process);
            $em->flush();

            return $this->redirectToRoute('process_show', ['id' => $process->getId()]);
        }

        return $this->render(
            '@KontrolgruppenCore/process/resume.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'data' => $data,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/search-taxonomy/{taxonomy}", name="process_search_taxonomy", methods={"GET"})
     *
     * @param Request                $request
     * @param string                 $taxonomy
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface    $serializer
     *
     * @return Response
     *
     * @throws \JsonException
     */
    public function searchTaxonomy(Request $request, string $taxonomy, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $taxonomyClassName = [
            'processType' => ProcessTypeEntity::class,
        ][$taxonomy] ?? null;

        if (null === $taxonomyClassName) {
            throw new BadRequestHttpException(sprintf('Invalid taxonomy: %s', $taxonomy));
        }

        $clientType = $request->get('clientType');

        $repository = $entityManager->getRepository($taxonomyClassName);
        if (!$repository instanceof AbstractTaxonomyRepository) {
            throw new BadRequestHttpException(sprintf('Invalid taxonomy: %s', $taxonomy));
        }

        $items = $repository->findByClientType($clientType);
        $items = array_values($items);

        $json = $serializer->serialize($items, 'json', ['groups' => 'taxonomy_read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * Handle taxonomy callback.
     *
     * We need this callback to rebuild the process form when changing taxonomy
     * values (cf. ProcessType::buildForm).
     *
     * This will submit the form, but validation will fail due to missing csfr
     * token and hence a new process will not be created on each callback.
     *
     * @param FormInterface $form
     * @param Request       $request
     * @param Process       $process
     */
    private function handleTaxonomyCallback(FormInterface $form, Request $request, Process $process)
    {
        if ('GET' === $request->getMethod()
            && null !== ($processData = $request->get('process'))
            && isset($processData['processType'])) {
            $form->submit($processData);
        }
    }
}
