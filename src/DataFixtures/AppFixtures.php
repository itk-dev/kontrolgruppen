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
use Kontrolgruppen\CoreBundle\Entity\BaseConclusion;
use Kontrolgruppen\CoreBundle\Entity\Client;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\QuickLink;
use Kontrolgruppen\CoreBundle\Entity\Service;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Entity\WeightedConclusion;
use Kontrolgruppen\CoreBundle\Entity\Channel;

class AppFixtures extends Fixture
{
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
            $i++;
            $entityProcessType = new ProcessType();
            $entityProcessType->setConclusionClass($i % 2 == 0 ? WeightedConclusion::class : BaseConclusion::class);
            $entityProcessType->setName($name);
            $manager->persist($entityProcessType);
            $processTypes[] = $entityProcessType;
        }

        $quickLink = new QuickLink();
        $quickLink->setHref('https://motorregister.skat.dk');
        $quickLink->setName('Motorregisteret');
        $manager->persist($quickLink);

        $process = new Process();
        $process->setProcessType($processTypes[0]);
        $process->setProcessStatus($processStatuses[0]);
        $process->setChannel($channels[0]);
        $process->setConclusion(new WeightedConclusion());
        $process->setClient(new Client());
        $process->setClientCPR('111111-1111');
        $manager->persist($process);

        $process = new Process();
        $process->setProcessType($processTypes[1]);
        $process->setProcessStatus($processStatuses[0]);
        $process->setChannel($channels[1]);
        $process->setConclusion(new BaseConclusion());
        $process->setClient(new Client());
        $process->setClientCPR('222222-2222');
        $manager->persist($process);

        $manager->flush();
    }
}
