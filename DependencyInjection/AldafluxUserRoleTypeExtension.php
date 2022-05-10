<?php

namespace Aldaflux\AldafluxUserRoleTypeBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class AldafluxUserRoleTypeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

  
        if (! isset($config['configs']["default"]))
        {
            $default["display"]="standard";
            $default["security_checked"]=true;
            $config['configs']["default"]=$default;
        }
        
        $container->setParameter('aldaflux_user_role_type.profiles', $config['profiles']);
        $container->setParameter( 'aldaflux_user_role_type.configs', $config['configs'] );
        
        $container->setParameter( 'aldaflux_user_role_type.label', $config['label'] );
        

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
                
        
        
    }
}
