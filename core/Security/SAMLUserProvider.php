<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Security;

use Kontrolgruppen\CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class SAMLUserProvider.
 */
class SAMLUserProvider implements UserProviderInterface
{
    /** @var UserManagerInterface */
    private $userManager;

    /** @var SAMLAuthenticator */
    private $saml;

    /**
     * SAMLUserProvider constructor.
     *
     * @param UserManagerInterface $userManager
     * @param SAMLAuthenticator    $saml
     */
    public function __construct(UserManagerInterface $userManager, SAMLAuthenticator $saml)
    {
        $this->userManager = $userManager;
        $this->saml = $saml;
    }

    /**
     * Get user.
     *
     * @param string $username
     * @param string $displayName
     * @param array  $credentials
     *
     * @return UserInterface|null
     *
     * @throws \OneLogin\Saml2\Error
     * @throws \OneLogin\Saml2\ValidationError
     */
    public function getUser(string $username, string $displayName, array $credentials)
    {
        $user = $this->userManager->findUserByUsername($username);

        if (null === $user) {
            $user = new User();
            $user->setUsername($username);

            if (empty($user->getName())) {
                $user->setName($displayName);
            }

            $roles = $this->saml->getRoles($credentials['SAMLResponse']);
            $user->setRoles($roles);

            $this->userManager->updateUser($user);
        }

        return $user;
    }

    /**
     * @param string $username
     *
     * @return UserInterface|null
     */
    public function loadUserByIdentifier(string $username): UserInterface
    {
        $user = $this->userManager->findUserByUsername($username);

        if (null === $user) {
            $user = new User();
            $user->setUsername($username);
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface|null
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', UserInterface::class, $user::class));
        }

        if (!$this->supportsClass($user::class)) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->userManager->getClass(), $user::class));
        }

        if (null === $reloadedUser = $this->userManager->findUserBy(['id' => $user->getUserIdentifier()])) {
            throw new UserNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getUserIdentifier()));
        }

        return $reloadedUser;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        $userClass = $this->userManager->getClass();

        return $userClass === $class || is_subclass_of($class, $userClass);
    }
}
