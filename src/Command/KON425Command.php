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
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class KON425Command (cf. https://jira.itkdev.dk/browse/KON-425).
 */
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

    /**
     * KON425Command constructor.
     *
     * @param ProcessManager         $processManager the process manager
     * @param EntityManagerInterface $entityManager  the entity manager
     */
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
        $io = new SymfonyStyle($input, $output);

        $force = $input->getOption('force');

        $processRepository = $this->entityManager->getRepository(Process::class);
        $processLogEntryRepository = $this->entityManager->getRepository(ProcessLogEntry::class);
        $qb = $processRepository->createQueryBuilder('p');
        /** @var Process[] $processes */
        $processes = $qb
            ->where($qb->expr()->like('p.caseNumber', ':etek'))
            ->setParameter('etek', 'ETEK-%')
            ->getQuery()->execute();

        $counter = 0;
        foreach ($processes as $process) {
            $io->section(sprintf(
                '% 6d/%d: %s (#%d)',
                ++$counter,
                \count($processes),
                $process->getCaseNumber(),
                $process->getId()
            ));

            // Dig for first time completedAt has been set on process.
            /** @var ProcessLogEntry[] $logEntries */
            $logEntries = $processLogEntryRepository->getAllLogEntries($process);
            // If the log entries don't tell otherwise, we use the case's completed at date.
            $completedAt = $process->getCompletedAt();
            foreach ($logEntries as $logEntry) {
                $data = $logEntry->getLogEntry()->getData();
                if (isset($data['completedAt'])) {
                    $completedAt = $data['completedAt'];
                }
            }

            if (null === $completedAt) {
                $output->writeln(sprintf(
                    'Cannot find completed at for process %s (#%d)',
                    $process->getCaseNumber(),
                    $process->getId()
                ));
                continue;
            }

            $io->writeln(sprintf('Completed at %s', $completedAt->format(\DateTimeInterface::ATOM)));

            if (null !== $process->getOriginallyCompletedAt()) {
                $io->warning(sprintf(
                    'Originally completed at %s. Skipping.',
                    $process->getOriginallyCompletedAt()->format(\DateTimeInterface::ATOM)
                ));
            } else {
                if (!$force) {
                    $io->warning(sprintf(
                        'Not completing process %s (#%d) at %s (use `--force` to make it happen).',
                        $process->getCaseNumber(),
                        $process->getId(),
                        $completedAt->format(\DateTimeInterface::ATOM)
                    ));
                } else {
                    $io->success(sprintf(
                        'Completing process %s (#%d) at %s',
                        $process->getCaseNumber(),
                        $process->getId(),
                        $completedAt->format(\DateTimeInterface::ATOM)
                    ));
                    $this->processManager->completeProcess($process, $completedAt);
                }
            }

            $io->newLine();
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
