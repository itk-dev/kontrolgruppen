<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Command;

use Kontrolgruppen\CoreBundle\CPR\Cpr;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\CVR\Cvr;
use Kontrolgruppen\CoreBundle\CVR\CvrServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DataLookupCommand.
 */
class DataLookupCommand extends Command
{
    protected static $defaultName = 'kontrolgruppen:data:lookup';

    private CvrServiceInterface $cvrService;
    private CprServiceInterface $cprService;

    /**
     * ClientUpdateCommand constructor.
     *
     * @param CprServiceInterface $cprService
     * @param CvrServiceInterface $cvrService
     */
    public function __construct(CprServiceInterface $cprService, CvrServiceInterface $cvrService)
    {
        parent::__construct();
        $this->cprService = $cprService;
        $this->cvrService = $cvrService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Perform data lookup')
            ->addArgument('type', InputArgument::REQUIRED, 'The type of lookup: "CVR" or "CPR"')
            ->addArgument('ids', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The ids to look up');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Kontrolgruppen\CoreBundle\CPR\CprException
     * @throws \Kontrolgruppen\CoreBundle\CVR\CvrException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getArgument('type');
        $service = [
                'CPR' => $this->cprService,
                'CVR' => $this->cvrService,
            ][$type] ?? null;
        if (null === $service) {
            throw new InvalidArgumentException(sprintf('Invalid type: %s', $type));
        }

        $ids = $input->getArgument('ids');

        if ($service instanceof CprServiceInterface) {
            $table = new Table($output);
            $table->setHeaders([
                'cpr',
                'first name',
                'middle name',
                'last name',
                'street name',
                'house number',
                'side',
                'postal code',
                'city',
            ]);
            foreach ($ids as $cpr) {
                $result = $service->find(new Cpr($cpr));
                $table->addRow([
                    $cpr,
                    $result->getFirstName(),
                    $result->getMiddleName(),
                    $result->getLastName(),
                    $result->getStreetName(),
                    $result->getHouseNumber(),
                    $result->getSide(),
                    $result->getPostalCode(),
                    $result->getCity(),
                ]);
            }
            $table->render();
        }

        if ($service instanceof CvrServiceInterface) {
            $table = new Table($output);
            $table->setHeaders([
                'cvr',
                'name',
                'street name',
                'house number',
                'postal code',
                'city',
            ]);
            foreach ($ids as $cvr) {
                $result = $service->find(new Cvr($cvr));
                $table->addRow([
                    $cvr,
                    $result->getName(),
                    $result->getStreetName(),
                    $result->getHouseNumber(),
                    $result->getPostalCode(),
                    $result->getCity(),
                ]);
            }
            $table->render();
        }

        return 0;
    }
}
