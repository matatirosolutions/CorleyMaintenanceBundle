<?php
namespace Corley\MaintenanceBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CorleyMaintenanceExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // ... where all of the heavy logic is done
    }
}
