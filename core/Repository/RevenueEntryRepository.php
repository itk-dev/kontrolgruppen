<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kontrolgruppen\CoreBundle\Entity\RevenueEntry;

/**
 * @method RevenueEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method RevenueEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method RevenueEntry[]    findAll()
 * @method RevenueEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RevenueEntryRepository extends ServiceEntityRepository
{
    /**
     * RevenueEntryRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RevenueEntry::class);
    }
}
