<?php
namespace SeleniumSetup\Service;

use SeleniumSetup\Command\AbstractCommand;
use SeleniumSetup\Command\DownloadCommand;
use SeleniumSetup\Environment;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends AbstractCommand
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('start')
            ->setDescription('Start Selenium Server setup with all supported drivers attached to it.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the server.');
    }

    protected function runServer()
    {
        $this->command->stopSeleniumServer();
        if (!is_null($this->config->getProxyHost())) {
            $this->command->invalidateEnvProxy();
        }
        $this->command->addBuildFolderToPath();
        $this->command->startDisplay();
        $this->output->writeln(sprintf(
            'Starting Selenium Server (%s) ... %s:%s',
            $this->config->getName(),
            $this->config->getHostname(),
            $this->config->getPort()
        ));
        $this->command->startSeleniumServer();
        $this->output->writeln(sprintf(
            'Done. Test it at http://%s:%s/wd/hub/',
            $this->config->getHostname(),
            $this->config->getPort()
        ));

        return true;
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
        if ($this->env->test($output)) {
            $output->writeln('<comment>Everything good ...</comment>');
            $this->createFolders();
            $this->downloadDrivers();
            $this->runServer();
            return true;
        } else {
            $this->output->writeln('<error>Missing required components. Please review your setup.</error>');
            return false;
        }
    }


    protected function createFolders()
    {
        // Create the build folder. (Where the binaries will reside).
        if (!$this->fileSystem->isDir(($this->config->getBuildPath()))) {
            $this->fileSystem->createDir($this->config->getBuildPath());
        }
        // Create the tmp folder.
        if (!$this->fileSystem->isDir(($this->config->getTmpPath()))) {
            $this->fileSystem->createDir($this->config->getTmpPath());
            //putenv('TMP='.$this->config->getTmpPath());
            //putenv('TEMP='.$this->config->getTmpPath());
            //chown($this->config->getTmpPath(),666);
        }
        // Create the logs folder.
        if (!$this->fileSystem->isDir($this->config->getLogsPath())) {
            $this->fileSystem->createDir($this->config->getLogsPath());
        }
    }

    protected function downloadDrivers(OutputInterface $output)
    {
        foreach ($this->config->getBinaries() as $binary) {
            // Skip binaries that don't belong to the current operating system.
            if (
                !is_null($binary->getOs()) && $binary->getOs() != $this->env->getOsName() ||
                !is_null($binary->getOsType()) && $binary->getOsType() != $this->env->getOsType()
            ) {
                continue;
            }

            $binaryPath = $this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName();

            if (!$this->fileSystem->isFile($binaryPath)) {
                $output->writeln(sprintf(
                    'Downloading %s %s ...',
                    $binary->getLabel(),
                    $binary->getVersion()
                ));
                // Download.
                $downloadTo = $this->config->getBuildPath() . DIRECTORY_SEPARATOR . pathinfo($binary->getDownloadUrl(), PATHINFO_BASENAME);
                $command = $this->getApplication()->find('download');
                $greetInput = new ArrayInput([
                    'command' => 'download',
                    'from'    => $binary->getDownloadUrl(),
                    'to'  => $downloadTo
                ]);
                $returnCode = $command->run($greetInput, $output);

                // Unzip.
                if (in_array(pathinfo($binary->getDownloadUrl(), PATHINFO_EXTENSION), ['zip', 'tar', 'tar.gz'])) {
                    $zip = new \ZipArchive;
                    $res = $zip->open($downloadTo);
                    if ($res === true) {
                        $zip->extractTo(
                            $this->config->getBuildPath(),
                            [$binary->getBinName()]
                        );
                        $zip->close();
                    }
                } else {
                    $this->fileSystem->rename($downloadTo, $this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName());
                }

                // Make executable.
                $command = $this->getApplication()->find('makeExecutable');
                $greetInput = new ArrayInput([
                    'command' => 'makeExecutable',
                    'file'    => $this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName()
                ]);
                $returnCode = $command->run($greetInput, $output);
            } else {
                $output->writeln(sprintf(
                    'Skipping %s %s. Binary already exists.',
                    $binary->getLabel(),
                    $binary->getVersion()
                ));
            }
        }
    }
}