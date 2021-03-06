<?php

namespace CodeCloud\Bundle\ShopifyBundle\Api;

use GuzzleHttp\ClientInterface;

/**
 * Interface implemented by classes which create a Http Client
 * which is authenticated to access the specified store.
 */
interface HttpClientFactoryInterface
{
    /**
     * @param string $storeName
     * @param AppCredentialsInterface $credentials
     * @return ClientInterface
     */
    public function createHttpClient($storeName, AppCredentialsInterface $credentials);
}
