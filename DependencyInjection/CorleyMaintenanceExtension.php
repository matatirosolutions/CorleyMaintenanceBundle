<?php
namespace Corley\MaintenanceBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class CorleyMaintenanceExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('maintenance.page', $config['page']);
        $container->setParameter('maintenance.web', $config["web"]);
        $container->setParameter('maintenance.active_link_name', $config["active_link_name"]);
        $container->setParameter('maintenance.symlink', $config["symlink"]);

        if ($config["symlink"]) {
            $container->setAlias('corley_maintenance.strategy', 'corley_maintenance.strategy.symlink');
        } else {
            $container->setAlias('corley_maintenance.strategy', 'corley_maintenance.strategy.copy');
        }

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.xml');
    }
}
