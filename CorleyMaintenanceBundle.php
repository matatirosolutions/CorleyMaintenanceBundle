<?php
namespace Corley\MaintenanceBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CorleyMaintenanceBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

    }
}
