<?php

namespace Mrapps\OnesignalBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MrappsOnesignalExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('mrapps_onesignal.parameters.app_name', $config['parameters']['app_name']);
        $container->setParameter('mrapps_onesignal.parameters.app_id', $config['parameters']['app_id']);
        $container->setParameter('mrapps_onesignal.web_push.rest_api_key', $config['web_push']['rest_api_key']);
        $container->setParameter('mrapps_onesignal.web_push.gcm_sender_id', $config['web_push']['gcm_sender_id']);
        $container->setParameter('mrapps_onesignal.web_push.safari_web_id', $config['web_push']['safari_web_id']);


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
