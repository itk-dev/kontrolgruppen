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
use Kontrolgruppen\CoreBundle\Entity\Navigation;

/**
 * @method Navigation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Navigation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Navigation[]    findAll()
 * @method Navigation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NavigationRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Navigation::class);
    }

    /**
     * @return Process[]
     */
    public function findCompletedWithNoStatus(): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder
            ->where('p.completedAt IS NOT NULL')
            ->andWhere('p.processStatus IS NULL')
        ;

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find all processes utilizing batch processing.
     *
     * @param int $batchSize
     *
     * @return \Traversable|Process[]
     */
    public function findAllBatchProcessed(int $batchSize = 100): \Traversable
    {
        return SimpleBatchIteratorAggregate::fromQuery(
            $this->createQueryBuilder('p')->getQuery(),
            $batchSize
        );
    }

    /**
     * Find processes by client type and identifier.
     *
     * @param string $clientType
     * @param string $identifier
     *
     * @return Process[]
     */
    public function findByClientIdentifier(string $clientType, string $identifier)
    {
        return $this->createQueryBuilder('p')
            ->join('p.processClient', 'c')
            ->where('c.type = :client_type')
            ->setParameter('client_type', $clientType)
            ->andWhere('c.identifier = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getResult();
    }
}
