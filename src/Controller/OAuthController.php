<?php

namespace CodeCloud\Bundle\ShopifyBundle\Controller;

use CodeCloud\Bundle\ShopifyBundle\Event\PostAuthEvent;
use CodeCloud\Bundle\ShopifyBundle\Event\PreAuthEvent;
use CodeCloud\Bundle\ShopifyBundle\Exception\InsufficientScopeException;
use CodeCloud\Bundle\ShopifyBundle\Http\FrameBusterRedirectResponse;
use CodeCloud\Bundle\ShopifyBundle\Model\ShopifyStoreManagerInterface;
use CodeCloud\Bundle\ShopifyBundle\Security\HmacSignature;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Handles the OAuth handshake with Shopify.
 *
 * @see https://help.shopify.com/api/getting-started/authentication/oauth
 *
 * @Route("/shopify")
 */
class OAuthController
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var array
     */
    private $config;

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var ShopifyStoreManagerInterface
     */
    private $stores;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var HmacSignature
     */
    private $hmacSignature;

    /**
     * @param UrlGeneratorInterface $router
     * @param array $config
     * @param HttpClientInterface $client
     * @param ShopifyStoreManagerInterface $stores
     * @param EventDispatcherInterface $dispatcher
     * @param HmacSignature $hmacSignature
     */
    public function __construct(
        UrlGeneratorInterface $router,
        array $config,
        HttpClientInterface $client,
        ShopifyStoreManagerInterface $stores,
        EventDispatcherInterface $dispatcher,
        HmacSignature $hmacSignature
    ) {
        $this->router = $router;
        $this->client = $client;
        $this->stores = $stores;
        $this->config = (new OptionsResolver())
            ->setRequired(['api_key', 'shared_secret', 'scope', 'redirect_route'])
            ->resolve($config)
        ;
        $this->dispatcher = $dispatcher;
        $this->hmacSignature = $hmacSignature;
    }

    /**
     * Handles initial auth request from Shopify.
     *
     * @param Request $request
     * @return RedirectResponse
     *
     * @Route("/auth", name="codecloud_shopify_auth")
     */
    public function auth(Request $request)
    {
        if (!$storeName = $request->get('shop')) {
            throw new BadRequestHttpException('Request is missing required parameter "shop".');
        }

        if ($response = $this->dispatcher->dispatch(new PreAuthEvent($storeName))->getResponse()) {
            return $response;
        }

        // see: https://stackoverflow.com/a/37881509
        $verifyUrl = 'https:'.$this->router->generate('codecloud_shopify_verify', [], UrlGeneratorInterface::NETWORK_PATH);
        $nonce = uniqid();

        $this->stores->preAuthenticateStore($storeName, $nonce);

        $url = sprintf('https://%s/admin/oauth/authorize?', $storeName).http_build_query([
            'client_id'    => $this->config['api_key'],
            'scope'        => $this->config['scope'],
            'redirect_uri' => $verifyUrl,
            'state'        => $nonce,
        ]);

        return new FrameBusterRedirectResponse($url);
    }

    /**
     * Handles auth verification callback from Shopify.
     *
     * @param Request $request
     * @return string
     *
     * @Route("/verify", name="codecloud_shopify_verify")
     */
    public function verify(Request $request)
    {
        $authCode  = $request->get('code');
        $storeName = $request->get('shop');
        $nonce     = $request->get('state');
        $hmac      = $request->get('hmac');

        // todo validate store name
        // todo leverage options resolver?

        if (!$authCode || !$storeName || !$nonce || !$hmac) {
            throw new BadRequestHttpException('Request is missing one or more of required parameters: "code", "shop", "state", "hmac".');
        }

        if (!$this->hmacSignature->isValid($hmac, $request->query->all())) {
            throw new BadRequestHttpException('Invalid HMAC Signature');
        }

        $params = [
            'json' => [
                'client_id'     => $this->config['api_key'],
                'client_secret' => $this->config['shared_secret'],
                'code'          => $authCode
            ],
        ];

        // todo this can fail - 400
        $response = $this->client->request('POST', 'https://' . $storeName . '/admin/oauth/access_token', $params);
        $responseJson = json_decode($response->getContent(), true);

        if ($responseJson['scope'] != $this->config['scope']) {
            throw new InsufficientScopeException($this->config['scope'], $responseJson['scope']);
        }

        $accessToken = $responseJson['access_token'];
        $this->stores->authenticateStore($storeName, $accessToken, $nonce);

        if ($response = $this->dispatcher->dispatch(new PostAuthEvent($storeName, $accessToken))->getResponse()) {
            return $response;
        }

        return new RedirectResponse(
            $this->router->generate('codecloud_shopify_jwt', [
                'shop' => $storeName,
            ])
        );
    }
}
