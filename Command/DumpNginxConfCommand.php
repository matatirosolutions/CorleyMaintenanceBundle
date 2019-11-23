<?php
namespace Corley\MaintenanceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpNginxConfCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName("corley:maintenance:dump-nginx")
            ->setDescription("How to configure your Nginx web server")
            ->setHelp(<<<EOF
See how to configure your Nginx web server in order to enable/disable maintenance mode

    <info>php app/console corley:maintenance:dump-nginx</info>
EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $output->writeln(<<<EOF
Open your site configuration file and paste the following lines:

    location / {
        if (-f \$document_root/{$container->getParameter("maintenance.hard_lock")}) {
            return 503;
        }

        ### the rest of your config goes here ###
    }

    error_page 503 @maintenance;
    location @maintenance {
        expires           0;
        add_header        Cache-Control private;
        rewrite ^(.*)$ /{$container->getParameter("maintenance.hard_lock")} break;
    }

EOF
        );
    }
}
