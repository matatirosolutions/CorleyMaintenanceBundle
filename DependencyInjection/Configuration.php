<?php
namespace Corley\MaintenanceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('corley_maintenance');

        $rootNode
            ->children()
                ->scalarNode("page")->defaultValue(__DIR__ . '/../Resources/views/maintenance.html')->end()
                ->scalarNode("web")->defaultValue('%kernel.root_dir%/../web')->end()
                ->scalarNode("active_link_name")->defaultValue('maintenance.html')->end()
                ->booleanNode("symlink")->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
