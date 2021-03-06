<?php

namespace CodeCloud\Bundle\ShopifyBundle\Controller;

use CodeCloud\Bundle\ShopifyBundle\Model\ShopifyStoreManagerInterface;
use CodeCloud\Bundle\ShopifyBundle\Service\JwtResolver;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Handles JWT resolution to obtain a session id.
 *
 * @see https://shopify.dev/tutorials/authenticate-your-app-using-session-tokens#session-tokens
 *
 * @Route("/shopify")
 */
class JwtController
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var Environment
     */
    private $templating;

    /**
     * @var JwtResolver
     */
    private $jwtResolver;

    /**
     * @var ShopifyStoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    private $redirectRoute;

    /**
     * @param UrlGeneratorInterface $router
     * @param Environment $templating
     * @param JwtResolver $jwtResolver
     * @param string $redirectRoute
     */
    public function __construct(UrlGeneratorInterface $router, Environment $templating, JwtResolver $jwtResolver, ShopifyStoreManagerInterface  $storeManager, string $redirectRoute)
    {
        $this->router = $router;
        $this->templating = $templating;
        $this->jwtResolver = $jwtResolver;
        $this->storeManager = $storeManager;
        $this->redirectRoute = $redirectRoute;
    }

    /**
     * @Route("/jwt", name="codecloud_shopify_jwt")
     */
    public function showLanding(Request $request)
    {
        if ($request->isMethod('GET')) {
            return new Response($this->templating->render('@CodeCloudShopify/jwt.html.twig', [
                'shop' => $request->query->get('shop'),
            ]));
        }

        if (!$jwt = $request->request->get('jwt')) {
            throw new BadRequestException('missing jwt token');
        }

        $session = $this->jwtResolver->resolveJwt($jwt);
        $this->storeManager->authenticateSession($session);

        return new RedirectResponse(
            $this->router->generate($this->redirectRoute, [
                'shopify_session_id' => $session->sessionId,
            ])
        );
    }
}
