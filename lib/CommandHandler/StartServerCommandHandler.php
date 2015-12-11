<?php
namespace SeleniumSetup\CommandHandler;

use SeleniumSetup\Command\Environment\AddPathToGlobalPathCommand;
use SeleniumSetup\Command\System\DownloadCommand;
use SeleniumSetup\Command\System\KillCommand;
use SeleniumSetup\Command\System\MakeExecutableCommand;
use SeleniumSetup\Command\System\StartDisplayCommand;
use SeleniumSetup\Process\StartSeleniumProcess;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class StartServerCommandHandler extends AbstractCommandHandler
{
    protected function createFolders()
    {
        // Create the build folder. (Where the binaries will reside).
        if (!$this->fileSystem->isDir(($this->config->getBuildPath()))) {
            $this->fileSystem->createDir($this->config->getBuildPath());
        }
        
        // Create the tmp folder.
        if (!$this->fileSystem->isDir(($this->config->getTmpPath()))) {
            $this->fileSystem->createDir($this->config->getTmpPath());
        }
        
        // Create the logs folder.
        if (!$this->fileSystem->isDir($this->config->getLogsPath())) {
            $this->fileSystem->createDir($this->config->getLogsPath());
        }
    }

    protected function downloadDrivers()
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
                $this->output->writeln(
                    sprintf('Downloading %s %s ...', $binary->getLabel(), $binary->getVersion())
                );
                
                // Download.
                $downloadTo = $this->config->getBuildPath() . DIRECTORY_SEPARATOR . pathinfo($binary->getDownloadUrl(), PATHINFO_BASENAME);

                $command = new DownloadCommand();
                $commandInput = new ArrayInput([
                    'from'    => $binary->getDownloadUrl(),
                    'to'  => $downloadTo
                ]);
                $commandOutput = new BufferedOutput();
                $returnCode = $command->run($commandInput, $commandOutput);
                $this->output->writeln($commandOutput->fetch());

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
                $command = new MakeExecutableCommand();
                $commandInput = new ArrayInput([
                    'file'    => $this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName()
                ]);
                $commandOutput = new BufferedOutput();
                $returnCode = $command->run($commandInput, $commandOutput);
            } else {
                $this->output->writeln(
                    sprintf('Skipping %s %s. Binary already exists.', $binary->getLabel(), $binary->getVersion())
                );
            }
        }
    }
    
    public function test()
    {
        return $this->env->test($this->output);
    }
    
    public function handle()
    {
        $this->createFolders();
        $this->downloadDrivers();
        
        // Kill existing Selenium instance.
        $command = new KillCommand();
        $commandInput = new ArrayInput([
            'taskName'    => 'selenium'
        ]);
        $commandOutput = new BufferedOutput();
        $returnCode = $command->run($commandInput, $commandOutput);
        //$this->output->writeln($commandOutput->fetch());
        
        // Add build folder to path.
        $command = new AddPathToGlobalPathCommand();
        $commandInput = new ArrayInput([
            'path'    => $this->config->getBuildPath()
        ]);
        $commandOutput = new BufferedOutput();
        $returnCode = $command->run($commandInput, $commandOutput);
        //$this->output->writeln($commandOutput->fetch());
        
        // Start display.
        $command = new StartDisplayCommand();
        $commandInput = new ArrayInput([]);
        $commandOutput = new BufferedOutput();
        $returnCode = $command->run($commandInput, $commandOutput);
        //$this->output->writeln($commandOutput->fetch());

        // Start Selenium Server instance.
        $this->output->writeln(
            sprintf('Starting Selenium Server (%s) ... %s:%s', $this->config->getName(), $this->config->getHostname(), $this->config->getPort())
        );
        // @todo Create StartSeleniumProcessArgs
        $process = new StartSeleniumProcess(
            [
                'binary' => $this->config->getBuildPath() . $this->config->getBinary('selenium')->getBinName(),
                'port' => $this->config->getPort(),
                'proxyHost' => $this->config->getProxyHost(),
                'proxyPort' => $this->config->getProxyPort(),
                'log' => $this->config->getLogsPath() . 'selenium.log'
            ],
            $this->env
        );
        $pid = $process->start();
        var_dump($pid);


        $this->output->writeln(
            sprintf('Done. Test it at http://%s:%s/wd/hub/', $this->config->getHostname(), $this->config->getPort())
        );
    }
}