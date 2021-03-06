<?php

namespace CodeCloud\Bundle\ShopifyBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dispatched when a shop requests authorization, before authorization begins.
 *
 * This event allows you to decline authorizing certain stores or redirect
 * to a more elaborate signup process.
 */
class PreAuthEvent extends Event
{
    const NAME = 'codecloud_shopify.pre_auth';

    /**
     * @var string
     */
    private $shop;

    /**
     * @var Response
     */
    private $response;

    /**
     * @param string $shop
     */
    public function __construct(string $shop)
    {
        $this->shop = $shop;
    }

    /**
     * @return string
     */
    public function getShop(): string
    {
        return $this->shop;
    }

    /**
     * @return Response
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }
}
