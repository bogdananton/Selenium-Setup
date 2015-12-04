<?php
namespace SeleniumSetup;

use SeleniumSetup\Config\ConfigFactory;
use SeleniumSetup\Service\StartServerService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FrontController implements FrontControllerInterface
{
    protected $input;
    protected $output;
    protected $config;
    
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->config = ConfigFactory::createFromConfigFile(dirname(__FILE__) . '/../selenium-setup.json');
    }

    public function start()
    {
        
        $startService = new StartServerService($this->config, $this->input, $this->output);
        $startService->startServer();
    }

    public function stop()
    {
        $startService = new StartServerService($this->config, $this->input, $this->output);
        $startService->stopServer();
    }

    public function selfTest()
    {
        $startService = new StartServerService($this->config, $this->input, $this->output);
        $startService->runSelfTest();
    }
}
