<?php
namespace Corley\MaintenanceBundle\Maintenance\Strategy;

class CopyStrategy extends BaseStrategy
{
    public function put($sourceFile, $maintenanceFile)
    {
        copy($sourceFile, $maintenanceFile);

        return $maintenanceFile;
    }
}
