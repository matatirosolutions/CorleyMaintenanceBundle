<?php
namespace Corley\MaintenanceBundle\Tests\Command;

use Corley\MaintenanceBundle\Command\DumpNginxConfCommand;
use Corley\MaintenanceBundle\Command\DumpApacheConfCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use \PHPUnit\Framework\TestCase;


class DumpCommandTest extends TestCase
{
    private $hardLock = 'lock.html';

    public function testDumpApacheConfig()
    {
        $application = new Application();
        $application->add(new DumpApacheConfCommand($this->hardLock));

        $command = $application->find('corley:maintenance:dump-apache');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals($this->expectedApacheConfig(), trim($commandTester->getDisplay()));
    }

    public function testDumpNginxConfig()
    {
        $application = new Application();
        $application->add(new DumpNginxConfCommand($this->hardLock));

        $command = $application->find('corley:maintenance:dump-nginx');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals($this->expectedNginxConfig(), trim($commandTester->getDisplay()));
    }

    private function expectedApacheConfig(): string
    {
        return <<<EOF
Open your .htaccess file and paste the following lines before any other rewrite rules:

    <IfModule mod_rewrite.c>
      RewriteEngine on
      RewriteCond %{DOCUMENT_ROOT}/{$this->hardLock} -f
      RewriteCond %{SCRIPT_FILENAME} !{$this->hardLock}
      RewriteRule ^.*$ /{$this->hardLock} [R=503,L]

      RewriteCond %{DOCUMENT_ROOT}/{$this->hardLock} -f
      RewriteRule ^(.*)$ - [env=MAINTENANCE:1]

      <IfModule mod_headers.c>
        Header set cache-control "max-age=0,must-revalidate,post-check=0,pre-check=0" env=MAINTENANCE
        Header set Expires -1 env=MAINTENANCE
      </IfModule>
    </IfModule>

    ErrorDocument 503 /{$this->hardLock}
EOF;
    }

    private function expectedNginxConfig(): string
    {
        return <<<EOF
Open your site configuration file and paste the following lines:

    location / {
        if (-f \$document_root/{$this->hardLock}) {
            return 503;
        }

        ### the rest of your config goes here ###
    }

    error_page 503 @maintenance;
    location @maintenance {
        expires           0;
        add_header        Cache-Control private;
        rewrite ^(.*)$ /{$this->hardLock} break;
    }
EOF;
    }
}
