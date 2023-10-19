<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class UserLoginCommand.
 */
class UserLoginCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'kontrolgruppen:user:login';

    /**
     * UserLoginCommand constructor.
     *
     * @param UserRepository         $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface  $urlGenerator
     */
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager, private UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username')
            ->addArgument('destination', InputArgument::OPTIONAL, 'The destination');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        $user = $this->userRepository->find($username) ?? $this->userRepository->findOneBy(['username' => $username]);
        if (null === $user) {
            throw new RuntimeException(sprintf('Invalid username: %s', $username));
        }

        $token = uniqid('', true);
        $user->setCliLoginToken($token);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $parameters = [
            'cli-login-token' => $token,
        ];
        $destination = $input->getArgument('destination');
        if ($destination) {
            $parameters['destination'] = $destination;
        }

        $url = $this->urlGenerator->generate('cli_login', $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
        $output->writeln($url);

        return 0;
    }
}
