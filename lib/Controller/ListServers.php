<?php
namespace SeleniumSetup\Controller;

use SeleniumSetup\Config\ConfigFactory;
use SeleniumSetup\Environment;
use SeleniumSetup\Locker\Locker;
use SeleniumSetup\Locker\ServerItem;
use SeleniumSetup\Service\ListServersService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListServers extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('servers')
            ->setDescription('List registered Selenium Servers.');
    }

    /**
     * Execute the command.
     * @todo Decide if this can be isolated into a service. 
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Prepare.
        $locker = new Locker();
        $locker->openLockFile();
        
        // View.
        $table = $this->getHelper('table');
        $table
            ->setHeaders(ServerItem::getAllProperties())
            ->setRows($locker->toArray());
        $table->render($output);
    }

}