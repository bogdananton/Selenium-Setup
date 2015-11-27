<?php
namespace SeleniumSetup;

use SeleniumSetup\Config\ConfigFactory;
use SeleniumSetup\Service\StartServerService;
use SeleniumSetup\System\System;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FrontController implements FrontControllerInterface
{
    protected $input;
    protected $output;
    protected $system;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->system = new System();
    }

    public function start()
    {
        $contents = $this->system->readFile(dirname(__FILE__) . '/../selenium-setup.json');
        $config = ConfigFactory::createFromJSON($contents);
        $startService = new StartServerService($config, $this->input, $this->output);
        $startService->startServer();
    }

    public function stop()
    {
        // TODO: Implement stopServer() method.
    }
}
