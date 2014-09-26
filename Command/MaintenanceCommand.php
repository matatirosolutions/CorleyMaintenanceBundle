<?php
namespace Corley\MaintenanceBundle\Command;

use Symfony\Component\Console\Input\InputArgument;

class MaintenanceCommand extends BaseMaintenanceCommand
{
    protected function configure()
    {
        $this
            ->setName("corley:maintenance:lock")
            ->setDescription("Enable/Disable maintenance mode")
            ->setDefinition(array(
                new InputArgument('status', InputArgument::REQUIRED, 'The final status')
            ))
            ->setHelp(<<<EOF
Enable or Disable the Maintenance mode

    <info>php app/console corley:maintenance:lock on</info>
EOF
        );
    }
}
