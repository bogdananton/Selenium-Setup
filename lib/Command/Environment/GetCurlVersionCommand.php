<?php
namespace SeleniumSetup\Command\Environment;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetCurlVersionCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('getCurlVersion')
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
        $process = new Process($this->executable(), realpath(dirname(__DIR__).'/../../../'), array_merge($_SERVER, $_ENV), null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }

    /**
     * Find the correct executable to run depending on the OS.
     *
     * @return string
     */
    protected function executable()
    {
        $cmd = 'curl -V';
        return $cmd;
    }
}