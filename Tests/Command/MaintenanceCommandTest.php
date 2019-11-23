<?php
namespace Corley\MaintenanceBundle\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use \PHPUnit\Framework\TestCase;
use Corley\MaintenanceBundle\Command\MaintenanceCommand;

class MaintenanceCommandTest extends TestCase
{
    private $runner;

    private function prepareCommand()
    {
        $this->runner = $this->createMock('Corley\\MaintenanceBundle\\Maintenance\\Runner', array(), array(), '', false, false);

        $application = new Application();
        $application->add(new MaintenanceCommand($this->runner));

        $command = $application->find('corley:maintenance:lock');
        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    /**
     *
     */
    public function testMaintenanceWantsOnOff()
    {
        $this->expectException('InvalidArgumentException');
        $commandTester = $this->prepareCommand();
        $commandTester->execute(
            array(
                'status'    => 'onoff',
            )
        );
    }

    /**
     * @dataProvider combinations
     */
    public function testEnableMaintenance($status, $enable)
    {
        $commandTester = $this->prepareCommand();

        $this->runner->expects($this->once())
            ->method("enableMaintenance")
            ->with($this->equalTo($enable));

        $commandTester->execute($status);
    }

    public function combinations()
    {
        return array(
            array(array('status' => 'on'), true),
            array(array('status' => 'off'), false),
        );
    }
}
