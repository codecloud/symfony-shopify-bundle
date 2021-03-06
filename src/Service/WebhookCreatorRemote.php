<?php

namespace CodeCloud\Bundle\ShopifyBundle\Service;

use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\ShopifyApiFactory;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Creates Webhooks to be received by a remote endpoint.
 */
class WebhookCreatorRemote extends AbstractWebhookCreator
{
    /**
     * @var string
     */
    private $webhookUrl;

    /**
     * @param ShopifyApiFactory $apis
     * @param string $webhookUrl
     */
    public function __construct(ShopifyApiFactory $apis, $webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;

        parent::__construct($apis);
    }

    /**
     * {@inheritdoc}
     */
    public function createWebhooks(string $storeName, array $topics)
    {
        $api = $this->apis->getForStore($storeName);

        foreach ($topics as $topic) {
            $webhook = GenericResource::create([
                'topic' => $topic,
                'address' => $this->webhookUrl,
                'format' => 'json',
            ]);

            $api->Webhook->create($webhook);
        }
    }
}
