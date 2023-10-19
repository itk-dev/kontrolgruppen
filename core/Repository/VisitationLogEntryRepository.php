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
use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\Entity\Visitation;
use Kontrolgruppen\CoreBundle\Entity\VisitationLogEntry;

/**
 * @method VisitationLogEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitationLogEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitationLogEntry[]    findAll()
 * @method VisitationLogEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitationLogEntryRepository extends ServiceEntityRepository
{
    protected $paginator;

    /**
     * VisitationLogEntryRepository constructor.
     *
     * @param \Doctrine\Persistence\ManagerRegistry   $registry
     *   The registry
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     *   The paginator
     */
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, VisitationLogEntry::class);
        $this->paginator = $paginator;
    }

    /**
     * Get the latest entries, paginated.
     *
     * @param Visitation $visitation
     *   The visitation the logs belong to
     * @param int        $page
     *   The pagination page
     * @param int        $limit
     *   The limit on number of results
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     *   The pagination result
     */
    public function getLatestEntriesPaginated(Visitation $visitation, int $page = 1, int $limit = 20)
    {
        return $this->paginator->paginate(
            $this->getLatestEntriesQuery($visitation),
            $page,
            $limit
        );
    }

    /**
     * Get the latest log entries.
     *
     * @param Visitation $visitation
     *   The visitation the logs belong to
     * @param                                           $level
     *   The log level
     *
     * @return \Doctrine\ORM\Query
     *   The query
     */
    public function getLatestLogEntries(Visitation $visitation, $level)
    {
        $qb = $this->createQueryBuilder(
            'visitationLogEntry',
            'visitationLogEntry.id'
        );
        $qb
            ->select(['visitationLogEntry', 'logEntry'])
            ->where('visitationLogEntry.visitation = :visitation')
            ->setParameter('visitation', $visitation)
            ->andWhere('visitationLogEntry.level = :level')
            ->setParameter('level', $level)
            ->innerJoin('visitationLogEntry.logEntry', 'logEntry')
            ->orderBy('logEntry.loggedAt', 'DESC');

        return $qb->getQuery();
    }

    /**
     * Get all log entries.
     *
     * @param Visitation $visitation
     *   The visitation the logs belong to
     *
     * @return mixed
     *   The result
     */
    public function getAllLogEntries(Visitation $visitation)
    {
        return $this->getLatestEntriesQuery($visitation)->getResult();
    }

    /**
     * Get all log entries utilizing batch visitedsing.
     *
     * @param Visitation $visitation
     * @param int        $batchSize
     *
     * @return \Traversable
     */
    public function getAllLogEntriesBatchVisited(Visitation $visitation, $batchSize = 100): \Traversable
    {
        return SimpleBatchIteratorAggregate::fromQuery(
            $this->getLatestEntriesQuery($visitation),
            $batchSize
        );
    }

    /**
     * Get the latest entries, as query.
     *
     * @param Visitation $visitation
     *   The visitation the logs belong to
     *
     * @return \Doctrine\ORM\Query
     *   The query
     */
    protected function getLatestEntriesQuery(Visitation $visitation)
    {
        $qb = $this->createQueryBuilder(
            'visitationLogEntry',
            'visitationLogEntry.id'
        );
        $qb
            ->select(['visitationLogEntry', 'logEntry'])
            ->where('visitationLogEntry.visitation = :visitation')
            ->setParameter('visitation', $visitation)
            ->innerJoin('visitation.logEntry', 'logEntry')
            ->orderBy('logEntry.loggedAt', 'DESC');

        return $qb->getQuery();
    }
}
