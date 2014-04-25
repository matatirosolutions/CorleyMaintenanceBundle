<?php
namespace Corley\MaintenanceBundle\Maintenance\Strategy;

abstract class BaseStrategy
{
    abstract public function put($sourceFile, $maintenanceFile);

    public function remove($maintenanceFile)
    {
        if (file_exists($maintenanceFile)) {
            unlink($maintenanceFile);
        }

        return $maintenanceFile;
    }
}
