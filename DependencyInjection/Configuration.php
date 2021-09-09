<?php

namespace Aldaflux\AldafluxUserRoleTypeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Symfony\Component\HttpKernel\Kernel;


/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('aldaflux_user_role_type');
        
        if (Kernel::VERSION_ID >= 40200) 
        {
            $rootNode = $treeBuilder->getRootNode();
        }
        else 
        {
            $rootNode = $treeBuilder->root('aldaflux_user_role_type');
        }
        
        $rootNode
                ->children()
                        ->arrayNode('configs')
                            ->arrayPrototype()
                            ->children()
                                ->enumNode('display')
                                    ->values(['all', 'standard', 'minimum'])->defaultValue('standard')
                                ->end()
                                ->scalarNode('profile')->end()
                                ->scalarNode('security_checked')->defaultValue('standard')->end()
                            ->end()
                        ->end()
                        ->end()
                        ->arrayNode('profiles')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->beforeNormalization()->ifString()->then(function (string $v) { return [$v]; })->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()

                    ->arrayNode('label')
                    ->addDefaultsIfNotSet()
                            ->children()
                                ->enumNode('display')
                                    ->values(['asItIs', 'word', 'traduction'])->defaultValue('word')
                                ->end()
                                ->scalarNode('translation_prefixe')->defaultValue('user.roles.')->end()
                    ->end()
                    ->end();

        
        
        return $treeBuilder;
    }


}
