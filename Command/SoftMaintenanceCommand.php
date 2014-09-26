<?php
namespace Corley\MaintenanceBundle\Command;

use Symfony\Component\Console\Input\InputArgument;

class SoftMaintenanceCommand extends BaseMaintenanceCommand
{
    protected function configure()
    {
        $this
            ->setName("corley:maintenance:soft-lock")
            ->setDescription("Enable/Disable soft maintenance mode")
            ->setDefinition(array(
                new InputArgument('status', InputArgument::REQUIRED, 'The final status')
            ))
            ->setHelp(<<<EOF
Enable or Disable the Maintenance mode

    <info>php app/console corley:maintenance:soft-lock on</info>
EOF
        );
    }
}
