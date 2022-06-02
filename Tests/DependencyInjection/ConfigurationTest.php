<?php
namespace Corley\MaintenanceBundle\Tests\DependencyInjection;

use Corley\MaintenanceBundle\DependencyInjection\Configuration;
use \PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    /**
     * @dataProvider getModes
     */
    public function testConfigTree($options, $results)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, array($options));

        $this->assertEquals($results["symlink"], $config["symlink"]);
        $this->assertEquals($results["soft_lock"], $config["soft_lock"]);
        $this->assertEquals($results["hard_lock"], $config["hard_lock"]);
        $this->assertEquals($results["web"], $config["web"]);
        $this->assertEquals($results["whitelist"], $config["whitelist"]);
        $this->assertRegExp("/{$results["page"]}$/i", $config["page"]);
    }

    public function getModes()
    {
        return array(
            array(
                array(),
                array(
                    'symlink' => false,
                    'soft_lock' => 'soft.lock',
                    'hard_lock' => 'hard.lock',
                    'web' => '%kernel.project_dir%/public',
                    'whitelist' => array(
                        'paths' => array(),
                        'ips' => array()
                    ),
                    'page' => "maintenance.html",
                )
            ),
            array(
                array('symlink' => true, 'whitelist' => array('paths' => array('/_'))),
                array(
                    'symlink' => true,
                    'soft_lock' => 'soft.lock',
                    'hard_lock' => 'hard.lock',
                    'web' => '%kernel.project_dir%/public',
                    'whitelist' => array(
                        'paths' => array('/_'),
                        'ips' => array()
                    ),
                    'page' => "maintenance.html",
                )
            ),
        );
    }
}
