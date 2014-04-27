<?php
namespace Corley\MaintenanceBundle\Tests\DependencyInjection;

use Corley\MaintenanceBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getModes
     */
    public function testConfigTree($options, $results)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, array($options));

        $this->assertEquals($results, $config);
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
                    'web' => '%kernel.root_dir%/../web',
                    'whitelist' => array(
                        'paths' => array(),
                        'ips' => array()
                    ),
                    'page' => realpath(__DIR__ . '/../../Resources/views/maintenance.html'),
                )
            ),
            array(
                array('symlink' => true, 'whitelist' => array('paths' => array('/_'))),
                array(
                    'symlink' => true,
                    'soft_lock' => 'soft.lock',
                    'hard_lock' => 'hard.lock',
                    'web' => '%kernel.root_dir%/../web',
                    'whitelist' => array(
                        'paths' => array('/_'),
                        'ips' => array()
                    ),
                    'page' => realpath(__DIR__ . '/../../Resources/views/maintenance.html'),
                )
            ),
        );
    }
}
