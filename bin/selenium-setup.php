<?php
// Use composers autoload.php if available
if (file_exists(dirname(__FILE__) . '/../vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/../vendor/autoload.php';
} elseif (file_exists(dirname(__FILE__) . '/../../../autoload.php')) {
    require_once dirname(__FILE__) . '/../../../autoload.php';
}

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);

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

$console = new Application('Selenium Setup', '3.0.1');

$getConfigurationFile = function (InputInterface $input)
{
    $configurationPath = $input->getOption('config');
    if (null === $configurationPath) {
        $path = \Phar::running();
        if ($path === '') {
            $path = dirname(__FILE__) . '/..';
        }
        $configurationPath = $path . '/' . \SeleniumSetup\Config\ConfigFactory::DEFAULT_CONFIGURATION_FILE;
    }

    return $configurationPath;
};

$console
    ->register('start')
    ->setDescription('Start Selenium server with all supported drivers attached to it.')
    ->setDefinition(array(
        new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Path to your Selenium configuration options.')
    ))
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($greetings, $getConfigurationFile) {
        $output->writeln($greetings);
        $configurationPath = $getConfigurationFile($input);
        $c = new \SeleniumSetup\FrontController($input, $output, $configurationPath);
        $c->start();
    });

$console
    ->register('stop')
    ->setDescription('Stop Selenium server.')
    ->setDefinition(array(
        new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Path to your Selenium configuration options.')
    ))
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($greetings, $getConfigurationFile) {
        // $output->writeln($greetings);
        $configurationPath = $getConfigurationFile($input);
        $c = new \SeleniumSetup\FrontController($input, $output, $configurationPath);
        $c->stop();
    });

$console
    ->register('exportConfiguration')
    ->setDescription('Exports the current configuration file.')
    ->setDefinition(array(
        new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Path to your Selenium configuration options.'),
        new InputArgument('output', InputArgument::OPTIONAL, 'Path to the output. Default: {buildpath}/selenium-setup.json.'),
    ))
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($greetings, $getConfigurationFile) {
        $configurationPath = $getConfigurationFile($input);
        $c = new \SeleniumSetup\FrontController($input, $output, $configurationPath);
        $c->exportConfiguration();
    });

$console
    ->register('selfTest')
    ->setDescription('Start Selenium server and run tests.')
    ->setDefinition(array(
        new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Path to your Selenium configuration options.')
    ))
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($greetings, $getConfigurationFile) {
        $output->writeln($greetings);
        $configurationPath = $getConfigurationFile($input);
        $c = new \SeleniumSetup\FrontController($input, $output, $configurationPath);
        $c->selfTest();
    });



$console->run();
