<?php
namespace Corley\MaintenanceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MaintenanceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName("corley:maintenance")
            ->setDescription("Enable/Disable maintenance mode")
            ->setDefinition(array(
                new InputArgument('status', InputArgument::REQUIRED, 'The final status')
            ))
            ->setHelp(<<<EOF
Enable or Disable the Maintenance mode

    <info>php app/console corley:maintenance on</info>
EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = $input->getArgument("status");

        if ($status != 'on' && $status != 'off') {
            throw new \InvalidArgumentException("You have to use 'on' or 'off'");
        }
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('status')) {
            $status = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose the final status: [on/off]: ',
                function($status) {
                    switch ($status) {
                        case 'on':
                            return "on";
                        case 'off':
                            return "off";
                        default:
                            throw new \InvalidArgumentException("You have to pass 'on' or 'off'");
                    }
                }
            );
            $input->setArgument('status', $status);
        }
    }
}
