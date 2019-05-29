<?php

/*
 * This file is part of aakb/kontrolgruppen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Kontrolgruppen\CoreBundle\DBAL\Types\JournalEntryEnumType;
use Kontrolgruppen\CoreBundle\Entity\BaseConclusion;
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Entity\QuickLink;
use Kontrolgruppen\CoreBundle\Entity\Service;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Entity\WeightedConclusion;
use Kontrolgruppen\CoreBundle\Entity\Channel;
use Kontrolgruppen\CoreBundle\Service\ProcessManager;

class AppFixtures extends Fixture
{
    private $processManager;

    /**
     * AppFixtures constructor.
     *
     * @param $processManager
     */
    public function __construct(ProcessManager $processManager)
    {
        $this->processManager = $processManager;
    }

    public function load(ObjectManager $manager)
    {
        // @TODO: Insert correct values.

        $processStatuses = [];
        $services = [];
        $processTypes = [];
        $channels = [];

        $processStatusNames = ['Opstartet', 'Afsluttet'];
        foreach ($processStatusNames as $name) {
            $entityProcessStatus = new ProcessStatus();
            $entityProcessStatus->setName($name);
            $manager->persist($entityProcessStatus);
            $processStatuses[] = $entityProcessStatus;
        }

        $serviceNames = ['Kontanthjælp', 'Økonomisk friplads'];
        foreach ($serviceNames as $name) {
            $entityService = new Service();
            $entityService->setName($name);
            $manager->persist($entityService);
            $services[] = $entityService;
        }

        $channelNames = ['Anonym', 'Politiet'];
        foreach ($channelNames as $name) {
            $entityChannel = new Channel();
            $entityChannel->setName($name);
            $manager->persist($entityChannel);
            $channels[] = $entityChannel;
        }

        $processTypeNames = ['Enlig forhold', 'Udlandsforhold', 'Bopælsforhold'];
        $i = 0;
        foreach ($processTypeNames as $name) {
            ++$i;
            $entityProcessType = new ProcessType();
            $entityProcessType->setConclusionClass(0 === $i % 2 ? WeightedConclusion::class : BaseConclusion::class);
            $entityProcessType->setName($name);
            $manager->persist($entityProcessType);
            $processTypes[] = $entityProcessType;
        }

        $quickLink = new QuickLink();
        $quickLink->setHref('https://motorregister.skat.dk');
        $quickLink->setName('Motorregisteret');
        $manager->persist($quickLink);

        $process = $this->processManager->newProcess(null, $processTypes[0]);
        $process->setProcessStatus($processStatuses[0]);
        $process->setChannel($channels[0]);
        $process->setClientCPR('111111-1111');
        $manager->persist($process);

        $journalEntry = new JournalEntry();
        $journalEntry->setProcess($process);
        $journalEntry->setTitle('Test notat');
        $journalEntry->setType(JournalEntryEnumType::NOTE);
        $journalEntry->setBody('Tekst feltet');
        $manager->persist($journalEntry);

        $manager->flush();

        $process = $this->processManager->newProcess(null, $processTypes[1]);
        $process->setProcessStatus($processStatuses[0]);
        $process->setChannel($channels[1]);
        $process->setClientCPR('222222-2222');
        $manager->persist($process);

        $manager->flush();
    }
}
