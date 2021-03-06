<?php

namespace CodeCloud\Bundle\ShopifyBundle\Api;

use CodeCloud\Bundle\ShopifyBundle\Model\ShopifyStoreManagerInterface;

/**
 * Creates ShopifyApi instances.
 */
class ShopifyApiFactory
{
    /**
     * @var ShopifyStoreManagerInterface
     */
    private $storeManager;

    /**
     * @var HttpClientFactoryInterface
     */
    private $httpClientFactory;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * @param ShopifyStoreManagerInterface $storeManager
     * @param HttpClientFactoryInterface $httpClientFactory
     * @param string $apiVersion
     */
    public function __construct(
        ShopifyStoreManagerInterface $storeManager,
        HttpClientFactoryInterface $httpClientFactory,
        $apiVersion = null
    ) {
        $this->storeManager = $storeManager;
        $this->httpClientFactory = $httpClientFactory;
        $this->apiVersion = $apiVersion;
    }

    /**
     * @param string $storeName
     * @return ShopifyApi
     */
    public function getForStore($storeName)
    {
        if ($this->storeManager instanceof CredentialsResolverInterface) {
            $credentials = $this->storeManager->getCredentials($storeName);
        } else {
            $accessToken = $this->storeManager->getAccessToken($storeName);
            $credentials = new PublicAppCredentials($accessToken);
        }

        $client = $this->httpClientFactory->createHttpClient($storeName, $credentials);

        return new ShopifyApi($client, $this->apiVersion);
    }
}
