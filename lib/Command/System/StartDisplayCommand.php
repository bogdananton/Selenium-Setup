<?php
namespace SeleniumSetup\Command\System;

use SeleniumSetup\Environment;
use SeleniumSetup\SeleniumSetup;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartDisplayCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('startDisplay')
            ->setDescription('');
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
            $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
            $process->run(function ($type, $line) use ($output) {
                $output->write($line);
            });
        }
    }

    /**
     * Find the correct executable to run depending on the OS.
     *
     * @param Environment $env
     * @return string
     */
    protected function executable(Environment $env)
    {
        if ($env->getOsName() == 'windows') {
            $cmd = null;
        } else if ($env->getOsName() == 'mac') {
            $cmd = '/sbin/start-stop-daemon --start --pidfile /tmp/custom_xvfb_99.pid --make-pidfile --background --exec /usr/bin/Xvfb -- :99 -ac -screen 0 1280x1024x16';
        } else {
            $cmd = '/sbin/start-stop-daemon --start --pidfile /tmp/custom_xvfb_99.pid --make-pidfile --background --exec /usr/bin/Xvfb -- :99 -ac -screen 0 1280x1024x16';
        }

        return $cmd;
    }
}