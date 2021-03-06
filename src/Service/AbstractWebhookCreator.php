<?php

namespace CodeCloud\Bundle\ShopifyBundle\Service;

use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\ShopifyApiFactory;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Base class for webhook creators.
 */
abstract class AbstractWebhookCreator implements WebhookCreatorInterface
{
    /**
     * @var ShopifyApiFactory
     */
    protected $apis;

    /**
     * @param ShopifyApiFactory $apis
     */
    public function __construct(ShopifyApiFactory $apis)
    {
        $this->apis = $apis;
    }

    /**
     * {@inheritdoc}
     */
    public function listWebhooks(string $storeName)
    {
        $api = $this->apis->getForStore($storeName);

        return $api->Webhook->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAllWebhooks(string $storeName)
    {
        $api = $this->apis->getForStore($storeName);

        foreach ($api->Webhook->findAll() as $webhook) {
            $api->Webhook->delete($webhook['id']);
        }
    }
}
