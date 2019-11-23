<?php
namespace Corley\MaintenanceBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use \PHPUnit\Framework\TestCase;

use Corley\MaintenanceBundle\DependencyInjection\CorleyMaintenanceExtension;

class CorleyMaintenanceExtensionTest extends TestCase
{
    /** @var ContainerBuilder */
    private $container;
    private $kernel;
    private $symlink;
    private $copy;

    public function setUp(): void
    {
        $this->kernel = $this->createMock('Symfony\\Component\\HttpKernel\\KernelInterface');

        $this->symlink = $this->getMockBuilder('Corley\MaintenanceBundle\Maintenance\Strategy\SymlinkStrategy')
            ->disableOriginalConstructor()
            ->getMock();

        $this->copy = $this->getMockBuilder('Corley\MaintenanceBundle\Maintenance\Strategy\CopyStrategy')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = new ContainerBuilder();

        $this->container->setParameter('kernel.root_dir', __DIR__);
        $this->container->set('kernel', $this->kernel);

        $this->container->set('corley_maintenance.strategy.symlink', $this->symlink);
        $this->container->set('corley_maintenance.strategy.copy', $this->copy);
    }

    /**
     * @dataProvider symlinkModes
     */
    public function testSymlinkAndCopyModes($config, $class)
    {
        $extension = new CorleyMaintenanceExtension();
        $extension->load($config, $this->container);

        $this->assertInstanceOf($class, $this->container->get('corley_maintenance.strategy'));
    }

    public function symlinkModes()
    {
        return array(
            array(array(array('symlink' => false)), 'Corley\MaintenanceBundle\Maintenance\Strategy\CopyStrategy'),
            array(array(array('symlink' => true)), 'Corley\MaintenanceBundle\Maintenance\Strategy\SymlinkStrategy'),
        );
    }
}
