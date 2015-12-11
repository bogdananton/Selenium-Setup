<?php
namespace SeleniumSetup\Command\Environment;

use SeleniumSetup\Environment;
use SeleniumSetup\EnvironmentInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddPathToGlobalPathCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('addPathToGlobalPath')
            ->setDescription('')
            ->addArgument('path', InputArgument::REQUIRED, 'The path.');
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
        putenv('PATH=' . getenv('PATH') . $this->getSeparator(new Environment()) . $input->getArgument('path'));
        $output->writeln(sprintf('Added %s to global path.', $input->getArgument('path')));
    }

    /**
     * Find the correct executable to run depending on the OS.
     *
     * @param EnvironmentInterface $env
     * @return string
     */
    protected function getSeparator(EnvironmentInterface $env)
    {
        if ($env->getOsName() == $env::OS_WINDOWS) {
            $separator = ';';
        } else if ($env->getOsName() == $env::OS_LINUX) {
            $separator = ':';
        } else {
            $separator = ':';
        }

        return $separator;
    }
}