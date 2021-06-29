<?php

/*
 * This file is part of aakb/kontrolgruppen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Service\ProcessManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class KON425Command extends Command
{
    protected static $defaultName = 'app:KON-425';

    /**
     * @var ProcessManager
     */
    private $processManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ProcessManager $processManager, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->processManager = $processManager;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Set missing completed at on ETEK processes')
            ->addOption('--force', null, InputOption::VALUE_NONE, 'Do stuff')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $force = $input->getOption('force');

        $processRepository = $this->entityManager->getRepository(Process::class);
        $processLogEntryRepository = $this->entityManager->getRepository(ProcessLogEntry::class);
        $qb = $processRepository->createQueryBuilder('p');
        /** @var Process[] $processes */
        $processes = $qb
//            ->where($qb->expr()->like('p.caseNumber', ':etek'))
//            ->setParameter('etek', 'ETEK-%')
            ->getQuery()->execute();

        foreach ($processes as $process) {
            $output->writeln(sprintf('%s (#%d)', $process->getCaseNumber(), $process->getId()));

            /** @var ProcessLogEntry[] $logEntries */
            $logEntries = $processLogEntryRepository->getAllLogEntries($process);
            $completedAt = null;
            foreach ($logEntries as $logEntry) {
                $data = $logEntry->getLogEntry()->getData();
                if (isset($data['completedAt'])) {
                    $completedAt = $data['completedAt'];
                    break;
                }
            }

            $output->writeln($completedAt ? sprintf('Logged completed at %s', $completedAt->format(\DateTimeInterface::ATOM)) : 'Not completed');

            if (null !== $completedAt) {
                continue;
            }

            if (null === $process->getOriginallyCompletedAt() && null !== $process->getCompletedAt()) {
                $output->writeln(json_encode([
                    '#logEntries' => \count($logEntries),
                    'process id' => $process->getId(),
                    'completed at' => $process->getCompletedAt()->format(DateTimeInterface::ATOM),
                ], JSON_PRETTY_PRINT));

                $completedAt = $process->getCompletedAt();

                $output->writeln(sprintf(
                    'Completing process %s (#%d) at %s',
                    $process->getCaseNumber(),
                    $process->getId(),
                    $completedAt->format(\DateTimeInterface::ATOM)
                ));
                if ($force) {
                    $this->processManager->completeProcess($process, $completedAt);

                    $logEntries = $processLogEntryRepository->getAllLogEntries($process);
                    $lastLogEntry = array_shift($logEntries)->getLogEntry();
                    $data = $lastLogEntry->getData();

                    var_export(['data' => $data]);

//                $data['completedAt'] = $completedAt;
//                $lastLogEntry->setData($data);
//                $this->entityManager->persist($lastLogEntry);
//                $this->entityManager->flush();
//
//                $data = $lastLogEntry->getData();
//
//                var_export(['data' => $data]);
                }
            }
        }

        return 0;
    }
}

/*

bin/console doctrine:database:drop --force --no-interaction
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate --no-interaction --quiet
bin/console doctrine:fixtures:load --no-interaction --quiet
bin/console app:KON-425

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE ext_log_entries;
TRUNCATE process_log_entry;
//*/
