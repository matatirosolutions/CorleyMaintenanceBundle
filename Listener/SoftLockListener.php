<?php
namespace Corley\MaintenanceBundle\Listener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\IpUtils;

class SoftLockListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    private $maintenancePage;
    private $lock;

    private $whitePaths;
    private $whiteIps;
    private $httpStatus;

    public function __construct($maintenancePage, $maintenanceLock, array $whitePaths, array $whiteIps, int $httpStatus = 503)
    {
        $this->maintenancePage = $maintenancePage;
        $this->lock = file_exists($maintenanceLock);
        $this->whiteIps = $whiteIps;
        $this->httpStatus = $httpStatus;

        array_walk($whitePaths, function(&$elem) {
            $elem = "/" . str_replace("/", "\\/", $elem) . "/";
        });

        $this->whitePaths = array_replace(array("/^\/_/"), $whitePaths);
    }

    public function setRequestStack($requestStack): void
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
            $response->setStatusCode($this->httpStatus);
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
        $currentRequest = $this->requestStack->getCurrentRequest();

        if (null === $currentRequest) {
            return true;
        }

        $clientIps = $currentRequest->getClientIps();
        $clientIp = end($clientIps);

        return !IpUtils::checkIp($clientIp, $this->whiteIps);
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
