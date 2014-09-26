<?php
namespace Corley\MaintenanceBundle\Tests\Maintenance\Strategy;

use org\bovigo\vfs\vfsStream;

class BaseStrategyTest extends \PHPUnit_Framework_TestCase
{
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('web');
    }

    public function testRemove()
    {
        $strategy = $this->getMockForAbstractClass('Corley\MaintenanceBundle\Maintenance\Strategy\BaseStrategy');
        file_put_contents(vfsStream::url('web/a'), "MAINTENANCE");

        $strategy->remove(vfsStream::url('web/a'));
        $this->assertFalse(file_exists(vfsStream::url('web/a')));
    }
}
