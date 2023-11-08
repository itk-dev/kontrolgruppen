<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\LockFactory;

/**
 * Class LockService.
 */
class LockService
{
    private $factory;
    private $locks = [];

    /**
     * LockService constructor.
     *
     * @param string $lockFactory
     */
    public function __construct(LockFactory $lockFactory)
    {
        $this->factory = $lockFactory;
    }

    /**
     * @return Factory
     */
    public function getFactory(): LockFactory
    {
        return $this->factory;
    }

    /**
     * @param string $resource
     *
     * @return Lock
     */
    public function createLock(string $resource): Lock
    {
        if (\array_key_exists($resource, $this->locks) && $this->locks[$resource] instanceof Lock) {
            return $this->locks[$resource];
        }

        $lock = $this->factory->createLock($resource);
        $this->locks[$resource] = $lock;

        return $lock;
    }

    /**
     * @param string $resource
     *
     * @return Lock
     */
    public function getLock(string $resource): Lock
    {
        if (!\array_key_exists($resource, $this->locks)) {
            throw new \InvalidArgumentException('Lock does not exist.');
        }

        return $this->locks[$resource];
    }

    /**
     * @param string $resource
     * @param bool   $blocking
     *
     * @return bool
     */
    public function acquire(string $resource, $blocking = false): bool
    {
        $lock = $this->getLock($resource);

        return $lock->acquire($blocking);
    }

    /**
     * @param string $resource
     *
     * @return bool
     */
    public function isAcquired(string $resource): bool
    {
        $lock = $this->getLock($resource);

        return $lock->isAcquired();
    }

    /**
     * @param string $resource
     */
    public function release(string $resource)
    {
        $lock = $this->getLock($resource);

        $lock->release();
    }
}
