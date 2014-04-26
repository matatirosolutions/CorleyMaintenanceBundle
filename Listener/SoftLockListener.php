<?php
namespace Corley\MaintenanceBundle\Listener;

use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;

class SoftLockListener
{
    private $requestStack;
    private $maintenancePage;
    private $lock;

    private $whitePaths;

    public function __construct($maintenancePage, $maintenanceLock, array $whitePaths)
    {
        $this->maintenancePage = $maintenancePage;
        $this->lock = file_exists($maintenanceLock);

        array_walk($whitePaths, function(&$elem) {
            $elem = "/" . str_replace("/", "\\/", $elem) . "/";
        });

        $this->whitePaths = array_replace(array("/^\/_/"), $whitePaths);
    }

    public function setRequestStack($requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest($event)
    {
        if ($this->isUnderMaintenance()) {
            $response = new Response();
            $response->setStatusCode(503);
            $response->setContent(file_get_contents($this->maintenancePage));

            $event->setResponse($response);
            $event->stopPropagation();
        }
    }

    private function isUnderMaintenance()
    {
        $path = $this->requestStack->getCurrentRequest()->getPathInfo();
        return ($this->lock && $this->isPathUnderMaintenance($path));
    }

    private function isPathUnderMaintenance($path)
    {
        foreach ($this->whitePaths as $pattern) {
            if (preg_match($pattern, $path)) {
                return false;
            }
        }

        return true;
    }
}
