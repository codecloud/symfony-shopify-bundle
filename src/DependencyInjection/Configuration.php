<?php

namespace CodeCloud\Bundle\ShopifyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder for the shopify configuration entries
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('code_cloud_shopify');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->arrayNode('oauth')->isRequired()
                ->children()
                    ->scalarNode('api_key')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('shared_secret')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('scope')->defaultValue('read_products')->end()
                    ->scalarNode('redirect_route')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->arrayNode('webhooks')
                ->prototype('scalar')->end()
                ->defaultValue([])
            ->end()
            ->scalarNode('webhook_url')->defaultNull()->end()
            ->scalarNode('api_version')->defaultNull()->end()
            ->scalarNode('dev_impersonate_store')->end()
            ->scalarNode('dev_impersonate_firewall')->defaultValue('main')->end()
        ->end();

        return $treeBuilder;
    }
}
