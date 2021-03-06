<?php

namespace CodeCloud\Bundle\ShopifyBundle\Service;

use CodeCloud\Bundle\ShopifyBundle\Model\Session;
use Firebase\JWT\JWT;

class JwtResolver
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $sharedSecret;

    /**
     * @param string $apiKey
     * @param string $sharedSecret
     */
    public function __construct(string $apiKey, string $sharedSecret)
    {
        $this->apiKey = $apiKey;
        $this->sharedSecret = $sharedSecret;
    }

    public function resolveJwt(string $jwt): Session
    {
        $decoded = JWT::decode($jwt, $this->sharedSecret, ['HS256']);

        // todo https://shopify.dev/tutorials/authenticate-your-app-using-session-tokens#obtain-session-details-manually

//        dump($decoded);
//        die();

        $session = new Session();
        $session->sessionId = $decoded->sid;
        $session->storeName = str_replace("https://", "", $decoded->dest);

        return $session;
    }
}
