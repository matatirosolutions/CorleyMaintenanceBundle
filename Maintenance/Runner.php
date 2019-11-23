<?php
namespace Corley\MaintenanceBundle\Maintenance;

use Corley\MaintenanceBundle\Maintenance\Strategy\BaseStrategy;

class Runner
{
    private $publicFolder;
    private $sourceFile;

    private $symlink;
    /** @var BaseStrategy */
    private $strategy;
    private $destinationFileName;

    public function __construct($publicFolder, $sourceFile)
    {
        $this->publicFolder = $publicFolder;
        $this->sourceFile = $sourceFile;

        $this->symlink = false;
        $this->destinationFileName = basename($sourceFile) . ".html";
    }

    public function setActiveLinkName($name)
    {
        $this->destinationFileName = $name;
    }

    public function setStrategy(BaseStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function enableMaintenance($status)
    {
        if (!file_exists($this->sourceFile)) {
            throw new \InvalidArgumentException("Source file {$this->sourceFile} is missing");
        }

        $maintenanceFile = false;
        if ($status) {
            $maintenanceFile = $this->strategy->put("{$this->sourceFile}", "{$this->publicFolder}/{$this->destinationFileName}");
        } else {
            $maintenanceFile = $this->strategy->remove("{$this->publicFolder}/{$this->destinationFileName}");
        }

        return $maintenanceFile;
    }
}
