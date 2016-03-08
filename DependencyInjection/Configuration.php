<?php

namespace Mrapps\OnesignalBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mrapps_onesignal');

        $rootNode
            ->children()
                ->arrayNode('parameters')
                    ->children()
                        ->scalarNode('app_id')->defaultValue('')->end()
                        ->scalarNode('rest_api_key')->defaultValue('')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
