<?php
namespace Corley\MaintenanceBundle\Command;

use Corley\MaintenanceBundle\Maintenance\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class BaseMaintenanceCommand extends Command
{
    private $runner;

    public function __construct(Runner $runner)
    {
        parent::__construct(null);
        $this->runner = $runner;
    }

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = $input->getArgument("status");

        if ($status != 'on' && $status != 'off') {
            throw new \InvalidArgumentException("You have to use 'on' or 'off'");
        }

        $this->runner->enableMaintenance(($status == 'off') ? false : true);
        return 0;
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('status')) {
            $helper = $this->getHelper("question");
            $question = new ChoiceQuestion('Please choose the final status: [on/off]', ['on', 'off'], 1);
            $question->setErrorMessage('Status %s is invalid.');

            $status = $helper->ask($input, $output, $question);

            $input->setArgument('status', $status);
        }
    }
}
