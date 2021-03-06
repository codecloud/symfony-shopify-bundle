<?php

namespace CodeCloud\Bundle\ShopifyBundle\Api;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Creates authenticated clients for public and private apps.
 */
class HttpClientFactory implements HttpClientFactoryInterface
{
    public function createHttpClient($storeName, AppCredentialsInterface $credentials)
    {
        $handlers = HandlerStack::create();
        $handlers->push(Middleware::retry(
            function ($retries, RequestInterface $request, ResponseInterface $response = null, \Exception $error = null) {

                // todo rate limit by this
                //$response->getHeaderLine('X-Shopify-Shop-Api-Call-Limit');
                if ($response && $response->getStatusCode() == Response::HTTP_TOO_MANY_REQUESTS) {
                    return true;
                }

                return false;
            },
            function ($retries, ResponseInterface $response) {
                if (!$response->hasHeader('Retry-After')) {
                    return 1000;
                }

                dump((float) $response->getHeaderLine('Retry-After') * 1000);
                die();

                return (float) $response->getHeaderLine('Retry-After') * 1000;
            }
        ));

        $options = [
            'base_uri' => 'https://' . $storeName,
            //'handler' => $handlers,
        ];

        switch (true) {
            case  $credentials instanceof PublicAppCredentials:
                $options['headers'] = [
                    'X-Shopify-Access-Token' => $credentials->getAccessToken(),
                ];
                break;
            case $credentials instanceof PrivateAppCredentials:
                $options['auth'] = [
                    $credentials->getApiKey(),
                    $credentials->getPassword(),
                ];
                break;
            default:
                throw new \RuntimeException('Invalid credentials given');
        }

        return new Client($options);
    }
}
