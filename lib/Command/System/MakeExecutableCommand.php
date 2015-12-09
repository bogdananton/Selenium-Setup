<?php
namespace SeleniumSetup\Command;

use SeleniumSetup\Environment;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeExecutableCommand extends AbstractCommand
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
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'The file path.');
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
            $cmd = null;
        } else if ($env->getOsName() == $env::OS_LINUX) {
            $cmd = 'chmod +x %s';
        } else {
            $cmd = 'chmod +x %s';
        }

        return $cmd;
    }
}