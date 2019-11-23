<?php
namespace Corley\MaintenanceBundle\Tests\Maintenance;

use \PHPUnit\Framework\TestCase;
use Corley\MaintenanceBundle\Maintenance\Runner;

class RunnerTest extends TestCase
{
    private $strategy;

    public function setUp(): void
    {
        $this->strategy = $this->createMock("Corley\\MaintenanceBundle\\Maintenance\\Strategy\\BaseStrategy");
    }

    public function testBasePutFile()
    {
        $runner = new Runner(__DIR__, __FILE__);

        $this->strategy->expects($this->once())
            ->method("put")
            ->with(__FILE__, __FILE__ . '.html')
            ->will($this->returnValue(__FILE__ . '.html'));

        $this->strategy->expects($this->never())
            ->method("remove")
            ->with($this->any());

        $runner->setStrategy($this->strategy);

        $path = $runner->enableMaintenance(true);

        $this->assertEquals(__FILE__ . '.html', $path);
    }

    public function testBaseRemoveFile()
    {
        $runner = new Runner(__DIR__, __FILE__);

        $this->strategy->expects($this->once())
            ->method("remove")
            ->with(__FILE__ . '.html')
            ->will($this->returnValue(__FILE__ . '.html'));

        $this->strategy->expects($this->never())
            ->method("put")
            ->with($this->any());

        $runner->setStrategy($this->strategy);

        $path = $runner->enableMaintenance(false);

        $this->assertEquals(__FILE__ . '.html', $path);
    }

    public function testRenamePutFile()
    {
        $runner = new Runner(__DIR__, __FILE__);
        $runner->setActiveLinkName("my-name");

        $this->strategy->expects($this->once())
            ->method("put")
            ->with(__FILE__, __DIR__ . '/my-name');

        $runner->setStrategy($this->strategy);

        $path = $runner->enableMaintenance(true);
    }

    public function testRenameRemoveFile()
    {
        $runner = new Runner(__DIR__, __FILE__);
        $runner->setActiveLinkName("my-name");

        $this->strategy->expects($this->once())
            ->method("remove")
            ->with(__DIR__ . '/my-name');

        $runner->setStrategy($this->strategy);

        $path = $runner->enableMaintenance(false);
    }
}
