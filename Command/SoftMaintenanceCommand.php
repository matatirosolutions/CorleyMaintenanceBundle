<?php
namespace Corley\MaintenanceBundle\Command;

use Corley\MaintenanceBundle\Maintenance\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Corley\MaintenananceBundle\Strategy\TouchStrategy;

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
