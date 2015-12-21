<?php
namespace SeleniumSetup;

use SeleniumSetup\Controller\StartServer;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SeleniumSetup
{
    const APP_NAME = 'Selenium Setup';
    const APP_VERSION = '4.0.0';
    const APP_DEFAULT_COMMAND = 'list';
    public static $APP_ROOT_PATH;
    public static $APP_CONF_PATH;
    public static $APP_PROCESS_ENV;
    
    const SSL_CERT_FILENAME = 'cacert.pem';
    const DEFAULT_LOCK_FILENAME = 'selenium-servers.lock';

    public static $BANNER = <<<'BANNER'
 ____            ___
/\  _`\         /\_ \                  __
\ \,\L\_\     __\//\ \      __    ___ /\_\  __  __    ___ ___
 \/_\__ \   /'__`\\ \ \   /'__`\/' _ `\/\ \/\ \/\ \ /' __` __`\
   /\ \L\ \/\  __/ \_\ \_/\  __//\ \/\ \ \ \ \ \_\ \/\ \/\ \/\ \
   \ `\____\ \____\/\____\ \____\ \_\ \_\ \_\ \____/\ \_\ \_\ \_\
    \/_____/\/____/\/____/\/____/\/_/\/_/\/_/\/___/  \/_/\/_/\/_/
    Selenium Environment on Windows, Linux and Mac
    by Bogdan Anton and contributors.

BANNER;
    
    public function __construct()
    {
        self::$APP_ROOT_PATH = realpath(dirname(__FILE__) . '/../');
        self::$APP_CONF_PATH = realpath(dirname(__FILE__) . '/../config/');
        self::$APP_PROCESS_ENV = array_merge($_SERVER, $_ENV);
    }
    
    public function run()
    {
        $console = new Application(self::APP_NAME, self::APP_VERSION);
        $console->setDefaultCommand(self::APP_DEFAULT_COMMAND);

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {
            // get the input instance
            // $input = $event->getInput();

            // get the output instance
            $output = $event->getOutput();

            // get the command to be executed
            $command = $event->getCommand();

            // write something about the command
            //$output->writeln(sprintf('Before running command <info>%s</info>', $command->getName()));
            if ($command->getName() == StartServer::CLI_COMMAND) {
                $output->write(self::$BANNER);
            }

            // get the application
            // $application = $command->getApplication();
        });
        $console->setDispatcher($dispatcher);

        $console->addCommands([
            new Controller\StartServer,
            new Controller\StopServer,
            new Controller\ListServers,
            new Controller\RegisterServer,
        ]);

        $console->run();
    }
}