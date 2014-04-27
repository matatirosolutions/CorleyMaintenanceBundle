<?php
namespace Corley\MaintenanceBundle\Command;

use Corley\MaintenanceBundle\Maintenance\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpApacheConfCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName("corley:maintenance:dump-apache")
            ->setDescription("How to configure your Apache web server")
            ->setHelp(<<<EOF
See how to configure your Apache2 web server in order to enable/disable maintenance mode

    <info>php app/console corley:maintenance:dump-apache</info>
EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $output->writeln(<<<EOF
Open your .htaccess file and paste those lines:

    RewriteCond %{DOCUMENT_ROOT}/{$container->getParameter("maintenance.hard_lock")} -f
    RewriteCond %{SCRIPT_FILENAME} !{$container->getParameter("maintenance.hard_lock")}
    RewriteRule ^.*$ /{$container->getParameter("maintenance.hard_lock")} [R=503,L]

    RewriteCond %{DOCUMENT_ROOT}/{$container->getParameter("maintenance.hard_lock")} -f
    RewriteRule ^(.*)$ - [env=MAINTENANCE:1]

    <IfModule mod_headers.c>
        Header set cache-control "max-age=0,must-revalidate,post-check=0,pre-check=0" env=MAINTENANCE
        Header set Expires -1 env=MAINTENANCE
    </IfModule>

    ErrorDocument 503 /{$container->getParameter("maintenance.hard_lock")}

EOF
        );
    }
}
