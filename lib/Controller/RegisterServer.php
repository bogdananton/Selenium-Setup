<?php
namespace SeleniumSetup\Controller;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterServer extends Command
{
    const CLI_COMMAND = 'register';
    
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(self::CLI_COMMAND)
            ->setDescription('Register a SeleniumSetup server instance.')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The config path.');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<error>Write me.</error>');
    }
}
