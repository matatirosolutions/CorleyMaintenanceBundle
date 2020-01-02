<?php
namespace Corley\MaintenanceBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpNginxConfCommand extends Command
{
    protected $hardLock;

    /**
     * DumpNginxConfCommand constructor.
     * @param $hardLock
     */
    public function __construct($hardLock)
    {
        parent::__construct(null);
        $this->hardLock = $hardLock;
    }


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
        $output->writeln(<<<EOF
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

EOF
        );

        return 0;
    }
}
