<?php
namespace Corley\MaintenanceBundle\Maintenance\Strategy;

class SymlinkStrategy extends BaseStrategy
{
    public function put($sourceFile, $maintenanceFile)
    {
        symlink($sourceFile, $maintenanceFile);
    }
}
