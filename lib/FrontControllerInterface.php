<?php
namespace SeleniumSetup;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface FrontControllerInterface
{
    public function __construct(InputInterface $input, OutputInterface $output);
    public function start();
    public function stop();
    public function selfTest();
}