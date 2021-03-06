<?php

namespace CodeCloud\Bundle\ShopifyBundle\Api;

use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CredentialsInterface;

interface CredentialsResolverInterface
{
    /**
     * @param string $storeName
     * @return CredentialsInterface
     */
    public function getCredentials($storeName);
}
