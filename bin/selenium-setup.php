<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

error_reporting(E_ERROR | E_PARSE);

$greetings = <<<'BANNER'
 ____            ___
/\  _`\         /\_ \                  __
\ \,\L\_\     __\//\ \      __    ___ /\_\  __  __    ___ ___
 \/_\__ \   /'__`\\ \ \   /'__`\/' _ `\/\ \/\ \/\ \ /' __` __`\
   /\ \L\ \/\  __/ \_\ \_/\  __//\ \/\ \ \ \ \ \_\ \/\ \/\ \/\ \
   \ `\____\ \____\/\____\ \____\ \_\ \_\ \_\ \____/\ \_\ \_\ \_\
    \/_____/\/____/\/____/\/____/\/_/\/_/\/_/\/___/  \/_/\/_/\/_/
    Selenium Environment using Facebook's WebDriver
    by Bogdan Anton and contributors.

BANNER;

$console = new Application('Selenium Setup', '3.0.0');

$console
    ->register('start')
    ->setDescription('Start Selenium server with all supported drivers attached to it.')
    ->setDefinition(array(
        new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Path to your Selenium configuration options.')
    ))
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($greetings) {
        $output->writeln($greetings);
        $c = new \SeleniumSetup\FrontController($input, $output);
        $c->start();
    });

$console
    ->register('stop')
    ->setDescription('Stop Selenium server.')
    ->setDefinition(array(
        new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Path to your Selenium configuration options.')
    ))
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($greetings) {
        // $output->writeln($greetings);
        $c = new \SeleniumSetup\FrontController($input, $output);
        $c->stop();
    });

$console
    ->register('selfTest')
    ->setDescription('Start Selenium server and run tests.')
    ->setDefinition(array(
        new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Path to your Selenium configuration options.')
    ))
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($greetings) {
        $output->writeln($greetings);
        $c = new \SeleniumSetup\FrontController($input, $output);
        $c->selfTest();
    });



$console->run();