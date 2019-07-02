<?php

/*
 * This file is part of aakb/kontrolgruppen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use FOS\UserBundle\Model\UserManagerInterface;
use Kontrolgruppen\CoreBundle\Entity\User;

class UserFixture extends AbstractFixture
{
    protected $class = User::class;

    /** @var \FOS\UserBundle\Model\UserManagerInterface */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    protected function buildEntity(array $data, $index)
    {
        if (!isset($data['username'])) {
            return null;
        }

        $username = $data['username'];
        $user = $this->userManager->findUserByUsername($username);
        if (!$user) {
            $user = $this->userManager->createUser();
        }
        $user
            ->setUsername($username)
            ->setEmail($data['email'] ?? $username)
            ->setEnabled(true);
        if (isset($data['password'])) {
            $user->setPlainPassword($data['password']);
        }
        foreach ($data as $key => $value) {
            if ($this->accessor->isWritable($user, $key)) {
                $this->accessor->setValue($user, $key, $value);
            }
        }

        return $user;
    }
}
