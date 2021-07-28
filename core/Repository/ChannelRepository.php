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
use Kontrolgruppen\CoreBundle\Entity\Channel;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;

/**
 * @method Channel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Channel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Channel[]    findAll()
 * @method Channel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChannelRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Channel::class);
    }

    /**
     * Get channels by process type.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\ProcessType $processType
     *   The process type
     *
     * @return mixed
     */
    public function getByProcessType(ProcessType $processType)
    {
        $qb = $this->createQueryBuilder('channel', 'channel.id')
            ->where(':processType MEMBER OF channel.processTypes')
            ->setParameter('processType', $processType)
            ->getQuery();

        return $qb->getResult();
    }
}
