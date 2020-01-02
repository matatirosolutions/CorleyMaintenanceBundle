<?php
namespace Corley\MaintenanceBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpApacheConfCommand extends Command
{
    protected $hardLock;

    /**
     * DumpApacheConfCommand constructor.
     * @param string $hardLock
     */
    public function __construct(string $hardLock)
    {
        parent::__construct(null);
        $this->hardLock = $hardLock;
    }


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
        $output->writeln(<<<EOF
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

EOF
        );

        return 0;
    }
}
