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
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Kontrolgruppen\CoreBundle\Entity\Visitation;
use Kontrolgruppen\CoreBundle\Entity\Service;
use Traversable;

/**
 * @method Visitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visitation[]    findAll()
 * @method Visitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitationRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visitation::class);
    }

    /**
     * Get all visitations from a given year.
     *
     * @param $year
     *   The creation year
     *
     * @return mixed
     *   The visitations
     *
     * @throws \Exception
     *   Datetime exception
     */
    public function findAllFromYear($year)
    {
        $from = new \DateTime($year.'-01-01 00:00:00');
        $to = new \DateTime($year.'-12-31 23:59:59');

        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }
}
