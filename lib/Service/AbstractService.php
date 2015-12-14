<?php
namespace SeleniumSetup\Service;

use SeleniumSetup\Config\ConfigInterface;
use SeleniumSetup\Environment;
use SeleniumSetup\FileSystem;
use SeleniumSetup\Locker\Locker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractService
{
    protected $config;
    protected $fileSystem;
    protected $env;
    protected $locker;
    protected $input;
    protected $output;
    
    public function __construct(
        ConfigInterface $config,
        Environment $env,
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->config = $config;
        $this->fileSystem = new FileSystem();
        $this->env = $env;
        $this->locker = new Locker();
        $this->input = $input;
        $this->output = $output;
    }
    
    public function handle()
    {
        throw new \Exception('A service must implement the handle() method.');
    }

}