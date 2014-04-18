<?php
namespace Corley\MaintenanceBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Corley\MaintenanceBundle\Command\MaintenanceCommand;

class MaintenanceCommandTest extends \PHPUnit_Framework_TestCase
{
    private function prepareCommand()
    {
        $kernel = $this->getMock('Symfony\Component\HttpKernel\Kernel', array(), array(), '', false, false);

        $application = new Application($kernel);
        $application->add(new MaintenanceCommand());

        $command = $application->find('corley:maintenance');
        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMaintenanceWantsOnOff()
    {
        $commandTester = $this->prepareCommand();
        $commandTester->execute(
            array(
                'status'    => 'onoff',
            )
        );
    }

    public function testEnableMaintenance()
    {
        $commandTester = $this->prepareCommand();
        $commandTester->execute(
            array(
                'status'    => 'on',
            )
        );
    }

    public function testDisableMaintenance()
    {
        $commandTester = $this->prepareCommand();
        $commandTester->execute(
            array(
                'status'    => 'off',
            )
        );
    }
}
