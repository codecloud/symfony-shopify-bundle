<?php

namespace CodeCloud\Bundle\ShopifyBundle\Model;

/**
 * This interface outlines all methods required to integrate Shopify OAuth with your application.
 */
interface ShopifyStoreManagerInterface
{
    /**
     * @param string $storeName
     * @return string
     */
    public function getAccessToken($storeName): string;

    /**
     * Return whether the specified store exists.
     *
     * @param string $storeName
     * @return bool
     */
    public function storeExists($storeName): bool;

    /**
     * This method is called when a store initiates authentication via Shopify OAuth.
     *
     * It is recommended that you store the `nonce` and `store name` for use in the following call
     * to `ShopifyStoreManagerInterface::authenticateStore`, but it is not strictly required.
     * If you want to forego the `nonce` check, you can leave this implementation empty.
     *
     * @param string $storeName
     * @param string $nonce
     */
    public function preAuthenticateStore($storeName, $nonce);

    /**
     * This method is called when the Shopify OAuth handshake completes successfully.
     *
     * It is your responsibility to store `access token` with the `store name` for use in
     * authentication. It is recommended that you check the previously issued nonce against the
     * one provided to this call, but this step is optional.
     *
     * @param string $storeName
     * @param string $accessToken
     * @param string $nonce
     */
    public function authenticateStore($storeName, $accessToken, $nonce);

    public function authenticateSession(Session $session);

    public function findStoreNameBySession(string $sessionId): ?string;
}
