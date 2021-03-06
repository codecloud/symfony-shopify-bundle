<?php

namespace CodeCloud\Bundle\ShopifyBundle\Api;

/**
 * Shopify Credentials used by Public Apps.
 * You generally obtain these via OAUTH handshake.
 */
class PublicAppCredentials implements AppCredentialsInterface
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @param string $accessToken
     */
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
