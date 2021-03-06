<?php

namespace CodeCloud\Bundle\ShopifyBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class SessionAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request)
    {
        return (bool) $this->getSessionId($request);
    }

    private function getSessionId(Request $request)
    {
        return $request->query->get('shopify_session_id', $request->get('shopify_session_id'));
    }

    public function getCredentials(Request $request)
    {
        return [
            'shopify_session_id' => $this->getSessionId($request),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['shopify_session_id']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        if (!$request->get('shop')) {
            return new Response('Your session has expired. Please access the app via Shopify Admin again.');
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('codecloud_shopify_auth', [
                'shop' => $request->get('shop'),
            ])
        );
    }
}
