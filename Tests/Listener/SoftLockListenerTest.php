<?php
namespace Corley\MaintenanceBundle\Test\Listener;

use Symfony\Component\HttpFoundation\Request;
use \PHPUnit\Framework\TestCase;
use Corley\MaintenanceBundle\Listener\SoftLockListener;

class SoftLockListenerTest extends TestCase
{
    private $event;
    private $requestStack;

    public function setUp(): void
    {
        $this->event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->requestStack = $this->createMock(
            'Symfony\\Component\\HttpFoundation\\RequestStack',
            array('getCurrentRequest')
        );
    }

    public function testNotUnderMaintenance()
    {
        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue(Request::create('/')));

        $listener = new SoftLockListener(__FILE__, __FILE__ . '.lock', array(), array());
        $listener->setRequestStack($this->requestStack);

        $listener->onKernelRequest($this->event);

        $this->assertNull($this->event->getResponse());
        $this->assertFalse($this->event->isPropagationStopped());
    }

    public function testUnderMaintenance()
    {
        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue(Request::create('/')));

        $listener = new SoftLockListener(__FILE__, __FILE__, array(), array());
        $listener->setRequestStack($this->requestStack);

        $listener->onKernelRequest($this->event);

        $this->assertNotNull($this->event->getResponse());
        $this->assertTrue($this->event->isPropagationStopped());
    }

    public function testNotUnderMaintenanceWhitePaths()
    {
        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue(Request::create('/_profiler')));

        $listener = new SoftLockListener(__FILE__, __FILE__, array(), array());
        $listener->setRequestStack($this->requestStack);

        $listener->onKernelRequest($this->event);

        $this->assertNull($this->event->getResponse());
        $this->assertFalse($this->event->isPropagationStopped());
    }

    public function testIpIsNotAllowed()
    {
        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue(
                Request::create('/the/app/path', "GET", array(), array(), array(), array('REMOTE_ADDR' => '127.0.0.1'))
            ));

        $listener = new SoftLockListener(__FILE__, __FILE__, array(), array());
        $listener->setRequestStack($this->requestStack);

        $listener->onKernelRequest($this->event);

        $this->assertNotNull($this->event->getResponse());
        $this->assertTrue($this->event->isPropagationStopped());
    }

    public function testIpIsAuthorized()
    {
        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue(
                Request::create('/the/app/path', "GET", array(), array(), array(), array('REMOTE_ADDR' => '127.0.0.1'))
            ));

        $listener = new SoftLockListener(__FILE__, __FILE__, array(), array("127.0.0.1"));
        $listener->setRequestStack($this->requestStack);

        $listener->onKernelRequest($this->event);

        $this->assertNull($this->event->getResponse());
        $this->assertFalse($this->event->isPropagationStopped());
    }
}

