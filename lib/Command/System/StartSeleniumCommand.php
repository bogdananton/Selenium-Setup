<?php
namespace SeleniumSetup\Command\System;

use SeleniumSetup\Environment;
use SeleniumSetup\SeleniumSetup;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartSeleniumCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('startSelenium')
            ->setDescription('')
            ->addArgument('binary', InputArgument::REQUIRED, 'The Selenium binary full path.')
            ->addArgument('port', InputArgument::REQUIRED, 'The port of the server.')
            ->addArgument('proxyHost', InputArgument::OPTIONAL, '')
            ->addArgument('proxyPort', InputArgument::OPTIONAL, '')
            ->addArgument('log', InputArgument::OPTIONAL, '');
    }
    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cmd = vsprintf($this->executable(new Environment()), $input->getArguments());
        $output->writeln($cmd);
        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }

    /**
     * Find the correct executable to run depending on the OS.
     *
     * @param Environment $env
     * @return string
     */
    protected function executable(Environment $env)
    {
        if ($env->getOsName() == $env::OS_WINDOWS) {
            $cmd = 'start /b java -jar %s -port %s -Dhttp.proxyHost=%s -Dhttp.proxyPort=%s -log %s';
        } else if ($env->getOsName() == $env::OS_LINUX) {
            $cmd = 'java -jar %s -port %s -Dhttp.proxyHost=%s -Dhttp.proxyPort=%s -log %s >/dev/null 2>&1 &';
        } else {
            $cmd = 'java -jar %s -port %s -Dhttp.proxyHost=%s -Dhttp.proxyPort=%s -log %s >/dev/null 2>&1 &';
        }

        return $cmd;
    }
}