<?php

namespace CodeCloud\Bundle\ShopifyBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class WebhookEvent extends Event
{
    const NAME = 'codecloud_shopify.webhook';

    /**
     * @var string
     */
    private $topic;

    /**
     * @var string
     */
    private $store;

    /**
     * @var array
     */
    private $resource;

    /**
     * @param string $topic
     * @param string $store
     * @param array $resource
     */
    public function __construct(string $topic, string $store, array $resource)
    {
        $this->topic = $topic;
        $this->store = $store;
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * @return string
     */
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * @return array
     */
    public function getResource(): array
    {
        return $this->resource;
    }
}
