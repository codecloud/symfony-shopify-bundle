<?php

namespace CodeCloud\Bundle\ShopifyBundle\Api;

/**
 * Shopify Credentials used by Private Apps.
 * You generally obtain these from the Shopify Admin Area.
 */
class PrivateAppCredentials implements AppCredentialsInterface
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $apiKey
     * @param string $password
     */
    public function __construct($apiKey, $password)
    {
        $this->apiKey = $apiKey;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
