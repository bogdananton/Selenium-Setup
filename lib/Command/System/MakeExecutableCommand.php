<?php
namespace SeleniumSetup\Command\System;

use SeleniumSetup\Environment;
use SeleniumSetup\EnvironmentInterface;
use SeleniumSetup\SeleniumSetup;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeExecutableCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('makeExecutable')
            ->setDescription('')
            ->addArgument('file', InputArgument::REQUIRED, 'The file path.');
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
        $cmd = $this->executable(new Environment());

        if (!is_null($cmd)) {
            $executable = sprintf($cmd, $input->getArgument('file'));
            $process = new Process($executable, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
            $process->run(function ($type, $line) use ($output) {
                $output->write($line);
            });
        }
    }

    /**
     * Find the correct executable to run depending on the OS.
     *
     * @param EnvironmentInterface $env
     * @return string
     */
    protected function executable(EnvironmentInterface $env)
    {
        if ($env->getOsName() == $env::OS_WINDOWS) {
            $cmd = null;
        } else if ($env->getOsName() == $env::OS_LINUX) {
            $cmd = 'chmod +x %s';
        } else {
            $cmd = 'chmod +x %s';
        }

        return $cmd;
    }
}