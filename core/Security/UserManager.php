<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Security;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserManager.
 */
class UserManager implements UserManagerInterface
{
    /** @var \Doctrine\ORM\EntityManagerInterface */
    protected $objectManager;

    /** @var string */
    protected $class;

    /**
     * LogManager constructor.
     *
     * @param ObjectManager $objectManager
     * @param string        $class
     */
    public function __construct(ObjectManager $objectManager, string $class)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;
    }

    /**
     * @return mixed|UserInterface
     */
    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class();

        return $user;
    }

    /**
     * @param UserInterface $user
     */
    public function deleteUser(UserInterface $user)
    {
        throw new \RuntimeException(sprintf('Lazy programmer exception: %s not implemented!', __METHOD__));
    }

    /**
     * @param array $criteria
     *
     * @return UserInterface|null
     */
    public function findUserBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * @param string $username
     *
     * @return UserInterface|null
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy(['username' => $username]);
    }

    /**
     * @return \Traversable|void
     */
    public function findUsers()
    {
        throw new \RuntimeException(sprintf('Lazy programmer exception: %s not implemented!', __METHOD__));
    }

    /**
     * @param UserInterface $user
     */
    public function reloadUser(UserInterface $user)
    {
        throw new \RuntimeException(sprintf('Lazy programmer exception: %s not implemented!', __METHOD__));
    }

    /**
     * @param UserInterface $user
     * @param bool          $andFlush
     */
    public function updateUser(UserInterface $user, bool $andFlush = true)
    {
        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        if (str_contains($this->class, ':')) {
            $metadata = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }

    /**
     * @return \Doctrine\Persistence\ObjectRepository
     */
    protected function getRepository()
    {
        return $this->objectManager->getRepository($this->getClass());
    }
}
