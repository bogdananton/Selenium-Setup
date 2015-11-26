<?php
require_once dirname(__FILE__) . '/../autoloader.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$console = new Application('Selenium Setup', '3.0.0');

$console
    ->register('start')
    ->setDescription('Start Selenium server with all supported drivers attached to it.')
    ->setDefinition(array(
        new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Path to your Selenium configuration options.')
    ))
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $c = new \PHPBI\Controller\FrontController($input, $output);
        $c->addProject();
    });