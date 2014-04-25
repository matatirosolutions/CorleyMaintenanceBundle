<?php
namespace Corley\MaintenanceBundle\Maintenance\Strategy;

class SymlinkStrategy extends BaseStrategy
{
    public function put($sourceFile, $maintenanceFile)
    {
        if (!file_exists($maintenanceFile)) {
            symlink($sourceFile, $maintenanceFile);
        }

        return $maintenanceFile;
    }
}
