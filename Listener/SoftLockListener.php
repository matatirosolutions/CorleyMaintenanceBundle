<?php
namespace Corley\MaintenanceBundle\Listener;

use Symfony\Component\HttpFoundation\Response;

class SoftLockListener
{
    private $requestStack;
    private $maintenancePage;
    private $lock;

    private $whitePaths;
    private $whiteIps;

    public function __construct($maintenancePage, $maintenanceLock, array $whitePaths, array $whiteIps)
    {
        $this->maintenancePage = $maintenancePage;
        $this->lock = file_exists($maintenanceLock);
        $this->whiteIps = $this->whiteIps;

        array_walk($whitePaths, function(&$elem) {
            $elem = "/" . str_replace("/", "\\/", $elem) . "/";
        });

        $this->whitePaths = array_replace(array("/^\/_/"), $whitePaths);
        $this->whiteIps = array_replace(array(), $whiteIps);
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
        return ($this->lock && $this->isIpNotAuthorized() && $this->isPathUnderMaintenance($path));
    }

    private function isIpNotAuthorized()
    {
        $currentIp = $this->requestStack->getCurrentRequest()->getClientIp();
        foreach ($this->whiteIps as $allowedIp) {
            if ($currentIp == $allowedIp) {
                return false;
            }
        }

        return true;
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
