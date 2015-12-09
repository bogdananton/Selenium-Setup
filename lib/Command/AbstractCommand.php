<?php
namespace SeleniumSetup\Command;

use SeleniumSetup\Config\ConfigInterface;
use SeleniumSetup\Environment;
use SeleniumSetup\FileSystem;
use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{
    protected $config;
    protected $fileSystem;
    protected $env;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->fileSystem = new FileSystem();
        $this->env = new Environment();

        parent::__construct();
    }
}