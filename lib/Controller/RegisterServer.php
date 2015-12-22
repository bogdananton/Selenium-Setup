<?php
namespace SeleniumSetup\Controller;

use SeleniumSetup\Config\ConfigFactory;
use SeleniumSetup\Environment;
use SeleniumSetup\Service\RegisterServerService;
use Symfony\Component\Console\Input\InputArgument;
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
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The config path.')
            ->addArgument('name', InputArgument::REQUIRED, 'Instance name.')
            ->addArgument('port', InputArgument::REQUIRED, 'Instance port.');
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
        $configFilePath = null;

        if ($input->getOption('config')) {
            $configFilePath = realpath($input->getOption('config'));
        }

        // Prepare.
        $config = ConfigFactory::createFromConfigFile($configFilePath);
        $env = new Environment($config, $input, $output);

        $handler = new RegisterServerService($config, $env, $input, $output);
        $handler->handle();
    }
}
