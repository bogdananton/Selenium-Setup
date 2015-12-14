<?php
namespace SeleniumSetup\Handler;

use SeleniumSetup\Config\ConfigInterface;
use SeleniumSetup\Environment;
use SeleniumSetup\FileSystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractHandler
{
    protected $config;
    protected $fileSystem;
    protected $env;
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
        $this->input = $input;
        $this->output = $output;
    }

}