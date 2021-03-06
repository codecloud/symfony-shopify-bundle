<?php
namespace CodeCloud\Bundle\ShopifyBundle\DependencyInjection;

use CodeCloud\Bundle\ShopifyBundle\Security\DevAuthenticator;
use CodeCloud\Bundle\ShopifyBundle\Security\DevEntryPoint;
use CodeCloud\Bundle\ShopifyBundle\Security\EntryPoint;
use CodeCloud\Bundle\ShopifyBundle\Security\ShopifyAdminUserProvider;
use CodeCloud\Bundle\ShopifyBundle\Service\WebhookCreator;
use CodeCloud\Bundle\ShopifyBundle\Service\WebhookCreatorInterface;
use CodeCloud\Bundle\ShopifyBundle\Service\WebhookCreatorLocal;
use CodeCloud\Bundle\ShopifyBundle\Service\WebhookCreatorRemote;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CodeCloudShopifyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('codecloud_shopify', $config);
        $container->setParameter('codecloud_shopify.oauth', $config['oauth']);
        $container->setParameter('codecloud_shopify.webhooks', $config['webhooks']);
        $container->setParameter('codecloud_shopify.webhook_url', $config['webhook_url']);
        $container->setParameter('codecloud_shopify.api_version', $config['api_version']);

        foreach ($config['oauth'] as $key => $value) {
            $container->setParameter('codecloud_shopify.oauth.'.$key, $value);
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (!empty($config['dev_impersonate_store'])) {
            $def = $container->getDefinition(ShopifyAdminUserProvider::class);
            $def->addMethodCall('setDevStore', [
                $config['dev_impersonate_store']
            ]);
        }

        if (!empty($config['webhook_url'])) {
            $container->setAlias(WebhookCreatorInterface::class, WebhookCreatorRemote::class);
        } else {
            $container->setAlias(WebhookCreatorInterface::class, WebhookCreatorLocal::class);
        }
    }
}
