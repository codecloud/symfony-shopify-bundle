<?php

namespace CodeCloud\Bundle\ShopifyBundle\Security;

use CodeCloud\Bundle\ShopifyBundle\Model\ShopifyStoreManagerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ShopifyAdminUserProvider implements UserProviderInterface
{
    const ROLE_SHOPIFY_ADMIN = 'ROLE_SHOPIFY_ADMIN';

    /**
     * @var ShopifyStoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    private $devStore;

    public function __construct(ShopifyStoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function setDevStore(string $devStore)
    {
        $this->devStore = $devStore;
    }

    public function loadUserByUsername($sessionId)
    {
        $storeName = $this->devStore ?? $this->storeManager->findStoreNameBySession($sessionId);

        if (!$storeName) {
            throw new UsernameNotFoundException();
        }

        return new ShopifyAdminUser($storeName, [self::ROLE_SHOPIFY_ADMIN]);
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return is_a($class, ShopifyAdminUser::class, true);
    }
}
