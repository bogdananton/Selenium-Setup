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
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function start()
    {
        $config = ConfigFactory::createFromConfigFile(dirname(__FILE__) . '/../selenium-setup.json');
        $startService = new StartServerService($config, $this->input, $this->output);
        $startService->startServer();
    }

    public function stop()
    {
        $config = ConfigFactory::createFromConfigFile(dirname(__FILE__) . '/../selenium-setup.json');
        $startService = new StartServerService($config, $this->input, $this->output);
        $startService->stopServer();
    }

    public function selfTest()
    {
        $config = ConfigFactory::createFromConfigFile(dirname(__FILE__) . '/../selenium-setup.json');
        $startService = new StartServerService($config, $this->input, $this->output);
        $startService->runSelfTest();
    }
}
