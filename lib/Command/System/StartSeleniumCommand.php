<?php
namespace SeleniumSetup\Command;

use SeleniumSetup\Environment;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartSeleniumCommand extends AbstractCommand
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
            ->addOption('host', 'h', InputOption::VALUE_REQUIRED, 'The host of the server.')
            ->addOption('port', 'p', InputOption::VALUE_REQUIRED, 'The port of the server.')
            ->addOption('proxyHost', 'pH', InputOption::VALUE_OPTIONAL, '')
            ->addOption('proxyPort', 'pP', InputOption::VALUE_OPTIONAL, '')
            ->addOption('log', 'l', InputOption::VALUE_OPTIONAL, '');
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
        $process = new Process($this->executable(new Environment()), realpath(__DIR__.'/../'), array_merge($_SERVER, $_ENV), null, null);
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
            $cmd = 'start /b java -jar ' . '%s' . ' -port %s' . ' -Dhttp.proxyHost=%s' . ' -Dhttp.proxyPort=%s' . ' -log %s';
        } else if ($env->getOsName() == $env::OS_LINUX) {
            $cmd = 'java -jar %s' . ' -port %s' . ' -Dhttp.proxyHost=%s' . ' -Dhttp.proxyPort=%s' . ' -log %s' . ' >/dev/null 2>&1 &';
        } else {
            $cmd = 'java -jar %s' . ' -port %s' . ' -Dhttp.proxyHost=%s' . ' -Dhttp.proxyPort=%s' . ' -log %s' . ' >/dev/null 2>&1 &';
        }

        return $cmd;
    }
}