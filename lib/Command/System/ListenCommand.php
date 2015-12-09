<?php
namespace SeleniumSetup\Command;

use SeleniumSetup\Environment\Environment;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListenCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('listen')
            ->setDescription('')
            ->addArgument('host', InputArgument::REQUIRED, 'The host of the server.')
            ->addArgument('port', InputArgument::REQUIRED, 'The port of the server.');
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
            $cmd = 'netstat -an|findstr :4444 %d';
        } else if ($env->getOsName() == 'mac') {
            $cmd = 'nc -z %s %d';
        } else {
            $cmd = 'nc -z %s %d';
        }

        return $cmd;
    }
}