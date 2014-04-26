<?php
namespace Corley\MaintenanceBundle\Maintenance\Strategy;

class TouchStrategy extends BaseStrategy
{
    public function put($sourceFile, $maintenanceFile)
    {
        touch($maintenanceFile);

        return $maintenanceFile;
    }
}
