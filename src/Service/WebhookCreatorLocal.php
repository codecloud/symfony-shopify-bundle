<?php

namespace CodeCloud\Bundle\ShopifyBundle\Service;

use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\ShopifyApiFactory;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Creates Webhooks to be received by this application.
 */
class WebhookCreatorLocal extends AbstractWebhookCreator
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @param ShopifyApiFactory $apis
     * @param UrlGeneratorInterface $router
     */
    public function __construct(ShopifyApiFactory $apis, UrlGeneratorInterface $router)
    {
        $this->router = $router;

        parent::__construct($apis);
    }

    /**
     * {@inheritdoc}
     */
    public function createWebhooks(string $storeName, array $topics)
    {
        $api = $this->apis->getForStore($storeName);

        foreach ($topics as $topic) {
            $endpoint = $this->router->generate('codecloud_shopify_webhooks', [
                'store' => $storeName,
                'topic' => $topic,
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            // endpoint HAS to be https
            $endpoint = str_replace("http://", "https://", $endpoint);

            $webhook = GenericResource::create([
                'topic' => $topic,
                'address' => $endpoint,
                'format' => 'json',
            ]);

            $api->Webhook->create($webhook);
        }
    }
}
