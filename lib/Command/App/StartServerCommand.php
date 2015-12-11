<?php
namespace SeleniumSetup\Command\App;

use SeleniumSetup\Command\AbstractCommand;
use SeleniumSetup\CommandHandler\StartServerCommandHandler;
use SeleniumSetup\Config\ConfigFactory;
use SeleniumSetup\Environment;
use SeleniumSetup\FileSystem;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartServerCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('start-server')
            ->setDescription('Start Selenium Server setup with all supported drivers attached to it.')
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
        // Prepare.
        $config = ConfigFactory::createFromConfigFile();
        $fileSystem = new FileSystem();
        $env = new Environment();
        
        $handler = new StartServerCommandHandler(
            $config,
            $fileSystem,
            $env,
            $input,
            $output
        );

        if ($handler->test()) {
            $output->writeln('<comment>Everything good ...</comment>');
            $handler->handle();
        } else {
            $output->writeln('<error>Missing required components. Please review your setup.</error>');
        }
        
    }

}