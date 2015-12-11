<?php
namespace SeleniumSetup\CommandHandler;

use SeleniumSetup\Config\ConfigInterface;
use SeleniumSetup\Environment;
use SeleniumSetup\FileSystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CommandHandlerInterface
{
    public function __construct(
        ConfigInterface $config,
        FileSystem $fileSystem,
        Environment $env,
        InputInterface $input,
        OutputInterface $output
    );
    
    public function test();
    
    public function handle();
}