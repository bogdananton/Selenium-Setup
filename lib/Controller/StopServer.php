<?php
namespace SeleniumSetup\Controller;

use SeleniumSetup\Config\ConfigFactory;
use SeleniumSetup\Environment;
use SeleniumSetup\Locker\Locker;
use SeleniumSetup\Service\StopServerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopServer extends Command
{
    const CLI_COMMAND = 'stop';
    
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(self::CLI_COMMAND)
            ->setDescription('Stop Selenium Server.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the server.');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $locker = new Locker();
        $locker->openLockFile();
        $serverItem = $locker->getServer($input->getArgument('name'));

        // Prepare.
        $config = ConfigFactory::createFromConfigFile($serverItem->getConfigFilePath());
        $env = new Environment($config, $input, $output);

        $handler = new StopServerService($config, $env, $input, $output);
        $handler->handle();
    }
}