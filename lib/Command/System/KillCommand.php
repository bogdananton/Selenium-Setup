<?php
namespace SeleniumSetup\Command;

use SeleniumSetup\Environment\Environment;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KillCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('kill')
            ->setDescription('')
            ->addArgument('taskName', InputArgument::REQUIRED, 'The task name.');
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
        if ($env->getOsName() == 'windows') {
            $cmd = 'taskkill /F /IM %s';
        } else if ($env->getOsName() == 'mac') {
            $cmd = 'pgrep -f "%s" | xargs kill';
        } else {
            $cmd = 'pgrep -f "%s" | xargs kill';
        }

        return $cmd;
    }
}