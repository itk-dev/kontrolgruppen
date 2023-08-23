<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CredentialsInterface;

/**
 * Class CliLoginTokenAuthenticator.
 */
class CliLoginTokenAuthenticator extends AbstractAuthenticator
{
    private $em;

    /**
     * CliLoginTokenAuthenticator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): ?bool
    {
        return 'cli_login' === $request->attributes->get('_route');
    }

    /**
     * @param Request $request
     * 
     * @return Passport
     * 
     * @throws AuthenticationException
     * @throws \OneLogin\Saml2\ValidationError
     */
    public function authenticate(Request $request): Passport
    {
        $cliLoginToken = $request->query->get('cli-login-token');

        if (null === $cliLoginToken) {
            throw new AuthenticationException('Invalid CLI login token.');
        }

        // if a User object, checkCredentials() is called
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['cliLoginToken' => $cliLoginToken]);

        // Clear out the token.
        if ($user !== null) {
            $user->setCliLoginToken(null);
            $this->em->persist($user);
            $this->em->flush();
        }

        return new Passport(new UserBadge($user->getUsername()), new CredentialsInterface(['cliLoginToken' => $cliLoginToken]));
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return RedirectResponse|Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?\Symfony\Component\HttpFoundation\Response
    {
        $destination = $request->get('destination') ?? '/';

        return new RedirectResponse($destination);
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?\Symfony\Component\HttpFoundation\Response
    {
        return new Response(strtr($exception->getMessageKey(), $exception->getMessageData()), Response::HTTP_FORBIDDEN);
    }
}
