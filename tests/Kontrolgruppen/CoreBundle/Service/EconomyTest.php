<?php

/*
 * This file is part of aakb/kontrolgruppen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Kontrolgruppen\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\DBAL\Types\RevenueTypeEnumType;
use Kontrolgruppen\CoreBundle\Entity\RevenueEntry;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Repository\RevenueEntryRepository;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Kontrolgruppen\CoreBundle\Service\EconomyService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class EconomyTest.
 */
class EconomyTest extends KernelTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * Test calculate function.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testCalculateFunction()
    {
        // Get special container that allows fetching private services
        $container = self::$container;

        // Get services.
        $processRepository = $container->get(ProcessRepository::class);
        $economyService = $container->get(EconomyService::class);
        $revenueEntryRepository = $container->get(RevenueEntryRepository::class);
        $serviceRepository = $container->get(ServiceRepository::class);
        $entityManager = $container->get(EntityManagerInterface::class);

        $process = $processRepository->findOneById(1);

        $revenueEntries = $revenueEntryRepository->findBy([
            'process' => $process,
        ]);

        $result = $economyService->calculateRevenue($process);

        $expected = [
            'entries' => $revenueEntries,
            'repaymentSum' => 2100.0,
            'repaymentSums' => [
                'Kontanthjælp' => [
                    'netPercentage' => 70.0,
                    'sum' => 1200.0,
                ],
                'Sygedagpenge' => [
                    'netPercentage' => 70.0,
                    'sum' => 900.0,
                ],
            ],
            'netRepaymentSum' => 1470.0,
            'futureSavingsSum' => 11900.0,
            'futureSavingsSums' => [
                'Kontanthjælp' => [
                    'netPercentage' => 70.0,
                    'sum' => 1100.0,
                ],
                'Sygedagpenge' => [
                    'netPercentage' => 70.0,
                    'sum' => 10800.0,
                ],
            ],
            'netFutureSavingsSum' => 8330.0,
            'netCollectiveSum' => 9800.0,
            'collectiveSum' => 14000.0,
        ];

        $this->assertEquals($expected, $result, 'Not expected results');

        $service = $serviceRepository->findOneBy(['name' => 'Kontanthjælp']);

        $newRevenueEntry = new RevenueEntry();
        $newRevenueEntry->setService($service);
        $newRevenueEntry->setProcess($process);
        $newRevenueEntry->setType(RevenueTypeEnumType::REPAYMENT);
        $newRevenueEntry->setAmount(500);

        $entityManager->persist($newRevenueEntry);
        $entityManager->flush();

        $result = $economyService->calculateRevenue($process);

        $this->assertEquals(2600.0, $result['repaymentSum'], 'Repayment does not match.');
        $this->assertEquals(14500.0, $result['collectiveSum'], 'Repayment does not match.');
    }
}
