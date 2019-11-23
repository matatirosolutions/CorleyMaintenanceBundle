<?php
namespace Corley\MaintenanceBundle\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

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
        $this->whiteIps = $whiteIps;

        array_walk($whitePaths, function(&$elem) {
            $elem = "/" . str_replace("/", "\\/", $elem) . "/";
        });

        $this->whitePaths = array_replace(array("/^\/_/"), $whitePaths);
    }

    public function setRequestStack($requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param GetResponseEvent|RequestEvent $event
     *
     * Note: To enable support for all currently supported versions of Symfony we can't
     * typehint the event since the event class changed between SF 4.4 and 5.0. As Nicolas
     * explained it the class isn't deprecated in 4.4 because the replacement relies on it.
     */
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
