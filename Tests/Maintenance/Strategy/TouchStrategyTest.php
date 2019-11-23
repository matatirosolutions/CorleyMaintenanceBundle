<?php
namespace Corley\MaintenanceBundle\Tests\Maintenance\Strategy;

use org\bovigo\vfs\vfsStream;
use \PHPUnit\Framework\TestCase;
use Corley\MaintenanceBundle\Maintenance\Strategy\TouchStrategy as Strategy;

class TouchStrategyTest extends TestCase
{
    private $root;

    public function setUp(): void
    {
        $this->root = vfsStream::setup('web');
    }

    public function testTouch()
    {
        $strategy = new Strategy();
        file_put_contents(vfsStream::url('web/a'), "MAINTENANCE");

        $strategy->put(vfsStream::url('web/a'), vfsStream::url('web/b'));

        $this->assertFileExists(vfsStream::url('web/b'));
        $this->assertEquals("", file_get_contents(vfsStream::url('web/b')));
    }
}
