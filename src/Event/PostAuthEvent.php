<?php

namespace CodeCloud\Bundle\ShopifyBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dispatched after a shop has been authorized to use the application.
 *
 * You can handle this event to handle the access token in whichever way your
 * authentication strategy requires. For example, store it in the session, or
 * pass it to a client.
 */
class PostAuthEvent extends Event
{
    const NAME = 'codecloud_shopify.post_auth';

    /**
     * @var string
     */
    private $shop;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var Response
     */
    private $response;

    /**
     * @param string $shop
     * @param string $accessToken
     */
    public function __construct(string $shop, string $accessToken)
    {
        $this->shop = $shop;
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getShop(): string
    {
        return $this->shop;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
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
