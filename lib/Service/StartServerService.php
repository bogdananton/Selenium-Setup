<?php
namespace SeleniumSetup\Service;

use SeleniumSetup\Locker\ServerItemFactory;

class StartServerService extends AbstractService
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
                $download = $this->env->download($binary->getDownloadUrl(), $downloadTo);
                $this->output->writeln($download);

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
                $this->env->makeExecutable($this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName());
            } else {
                $this->output->writeln(
                    sprintf('Skipping %s %s. Binary already exists.', $binary->getLabel(), $binary->getVersion())
                );
            }
        }
    }
    
    public function test()
    {
        return $this->env->test();
    }
    
    public function handle()
    {
        // @todo should allow only registered instances to be called

        $this->createFolders();
        $this->downloadDrivers();

        // Add build folder to path.
        $this->env->addPathToGlobalPath($this->config->getBuildPath());
        
        // Warn if Chrome or Firefox binaries are not available.
        if ($chromeVersion = $this->env->getChromeVersion()) {
            $this->output->writeln('Chrome binary found, v.' . $chromeVersion);
        } else {
            $this->output->writeln('<info>WARNING: Chrome binary not found.</info>');
        }

        if ($firefoxVersion = $this->env->getFirefoxVersion()) {
            $this->output->writeln('Firefox binary found, v.' . $firefoxVersion);
        } else {
            $this->output->writeln('<info>WARNING: Firefox binary not found.</info>');
        }
        
        // Warn if display is not available.
        if ($this->env->isLinux()) {
            if ($this->env->hasXvfb()) {
                $this->output->writeln('<info>Xvfb is installed. Good.</info>');
            } else {
                $this->output->writeln('<info>WARNING: Xvfb is not installed.</info>');
            }
        }
        
        // $pid = $this->env->startDisplayProcess();

        // Start Selenium Server instance.
        $this->output->writeln(
            sprintf('Starting Selenium Server (%s) ... %s:%s', $this->config->getName(), $this->config->getHostname(), $this->config->getPort())
        );

        $pid = $this->env->startSeleniumProcess();
        if ($pid > 0) {
            // Make sure that we capture the right PID.
            while ($this->env->listenToPort($this->config->getPort()) == '') {
                $this->output->write('<info>.</info>');
            }
            $parentPid = $this->env->getPidFromListeningToPort($this->config->getPort());
            if ($parentPid) {
                $pid = $parentPid;
            }

            // @todo open the lock file only to update the status and ports, the instance was already added.
            $this->locker->openLockFile();
            $this->locker->addServer(
                ServerItemFactory::createFromProperties(
                    $this->config->getName(),
                    $pid,
                    $this->config->getPort(),
                    $this->config->getFilePath()
                )
            );
            $this->locker->writeToLockFile();
        }

        $this->output->writeln('<info>Done</info>');
        $this->output->writeln(
            sprintf('Test it at http://%s:%s/wd/hub/', $this->config->getHostname(), $this->config->getPort())
        );
    }
}