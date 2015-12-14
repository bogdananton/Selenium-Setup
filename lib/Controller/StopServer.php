<?php
namespace SeleniumSetup\Controller;

use SeleniumSetup\SeleniumSetup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class StopServer extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('stop-server')
            ->setDescription('Stop Selenium Server.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the server.');
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
        $cmd = vsprintf($this->executable(), $input->getArguments());

        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
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
        if ($this->env->isWindows()) {
            $cmd = 'taskkill /F /IM java.exe';
        } else {
            $cmd = 'pgrep -f "selenium-setup.jar" | xargs kill';
        }

        return $cmd;
    }

}