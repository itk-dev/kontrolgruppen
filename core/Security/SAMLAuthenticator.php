<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Security;

use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Error;
use OneLogin\Saml2\IdPMetadataParser;
use OneLogin\Saml2\Response;
use OneLogin\Saml2\Settings;
use OneLogin\Saml2\Utils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CredentialsInterface;

/**
 * Class SAMLAuthenticator.
 */
class SAMLAuthenticator extends AbstractAuthenticator
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var array */
    private $settings;

    /**
     * SAMLAuthenticator constructor.
     *
     * @param RouterInterface  $router
     * @param array            $settings
     */
    public function __construct(RouterInterface $router, array $settings)
    {
        $this->router = $router;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): ?bool
    {
        return $this->router->generate('saml_acs') === $request->getPathInfo()
            && !empty($request->get('SAMLResponse'));
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
        $auth = $this->getAuth();

        $requestID = $_SESSION['AuthNRequestID'] ?? null;
        $auth->processResponse($requestID);
        if (isset($requestID)) {
            unset($_SESSION['AuthNRequestID']);
        }

        $errors = $auth->getErrors();

        if (!empty($errors)) {
            throw new AuthenticationException(implode(\PHP_EOL, $errors));
        }

        if (!$auth->isAuthenticated()) {
            throw new AuthenticationException('Not authenticated');
        }

        $username = $this->getUsername($auth);

        return new Passport(new UserBadge($username), new CredentialsInterface([
            'SAMLResponse' => $request->get('SAMLResponse'),
            'RelayState' => $request->get('RelayState'),
        ]));
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?\Symfony\Component\HttpFoundation\Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        $url = $this->router->generate('saml_failure');

        return new RedirectResponse($url);
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?\Symfony\Component\HttpFoundation\Response
    {
        // @TODO: Redirect to originally requested url.
        return new RedirectResponse('/');
    }

    /**
     * @return Auth
     *
     * @throws Error
     */
    public function getAuth()
    {
        // Make sure that we can handle running behind a proxy (cf.
        // https://github.com/onelogin/php-saml#url-guessing-methods).
        Utils::setProxyVars(true);

        return new Auth($this->getSettings());
    }

    /**
     * @param string $payload
     *
     * @return Response
     *
     * @throws Error
     * @throws \OneLogin\Saml2\ValidationError
     */
    public function getResponse(string $payload)
    {
        return new Response(new Settings($this->getSettings()), $payload);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getSettings()
    {
        if (isset($this->settings['idp']) && \is_string($this->settings['idp'])) {
            if (file_exists($this->settings['idp'])) {
                $data = IdPMetadataParser::parseFileXML($this->settings['idp']);
                if (isset($data['idp'])) {
                    $this->settings['idp'] = $data['idp'];
                }
            }
        }

        return $this->settings;
    }

    /**
     * @return bool
     */
    public function supportsSingleLogout()
    {
        return false;
    }

    /**
     * @param $samlResponse
     *
     * @return array
     *
     * @throws Error
     * @throws \OneLogin\Saml2\ValidationError
     */
    public function getRoles($samlResponse)
    {
        $roles = [];

        $settings = $this->getSettings();
        $response = new Response(new Settings($settings), $samlResponse);
        $attributes = $response->getAttributes();

        if (isset($settings['user_roles'])) {
            $userRoles = $settings['user_roles'];
            $attribute = $userRoles['attribute'] ?? 'http://schemas.xmlsoap.org/claims/Group';

            $samlRoles = $this->getSAMLRoles($attributes, $attribute);
            $rolesMap = $this->getRolesMap($userRoles);

            if (isset($rolesMap['CN'])) {
                $rolesMap = $rolesMap['CN'];
            }

            foreach ($samlRoles as $role) {
                if (isset($rolesMap[$role])) {
                    $roles[] = (array) $rolesMap[$role];
                }
            }
        }

        // Flatten the roles and make unique.
        if (!empty($roles)) {
            $roles = array_values(array_unique(array_merge(...$roles)));
        }

        if (empty($roles)) {
            throw new AuthenticationException('Cannot get user roles');
        }

        return $roles;
    }

    /**
     * @param Auth $auth
     *
     * @return mixed|string
     */
    private function getUsername(Auth $auth)
    {
        if (isset($this->settings['username_attribute_name'])) {
            $attribute = $auth->getAttribute($this->settings['username_attribute_name']);
            if (!empty($attribute)) {
                $username = reset($attribute);
                if (!empty($username)) {
                    return $username;
                }
            }
        }

        throw new AuthenticationException('Cannot get username');
    }

    /**
     * @param array  $attributes
     * @param string $attributeName
     *
     * @return array|mixed
     */
    private function getSAMLRoles(array $attributes, string $attributeName)
    {
        return $attributes[$attributeName] ?? [];
    }

    /**
     * @param $settings
     *
     * @return array
     */
    private function getRolesMap($settings)
    {
        $map = [];

        if (isset($settings['fields']) && \is_array($settings['fields'])) {
            foreach ($settings['fields'] as $name => $value) {
                $map[$name] = $value;
            }
        }

        return $map;
    }
}
