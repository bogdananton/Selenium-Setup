<?php
namespace SeleniumSetup\Handler;

use SeleniumSetup\SeleniumSetup;

class StartServerHandler extends AbstractHandler
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
        $this->createFolders();
        $this->downloadDrivers();
        
        // Kill existing Selenium instance.
        $this->env->killProcess('selenium');
        
        // Add build folder to path.
        $this->env->addPathToGlobalPath($this->config->getBuildPath());
        
        // Start display.
        $pid = $this->env->startDisplayProcess();

        // Start Selenium Server instance.
        $this->output->writeln(
            sprintf('Starting Selenium Server (%s) ... %s:%s', $this->config->getName(), $this->config->getHostname(), $this->config->getPort())
        );

        $pid = $this->env->startSeleniumProcess();
        if ($pid > 0) {
            // Create the lock file if it not exist.
            $lockFilePath = SeleniumSetup::$APP_ROOT_PATH . DIRECTORY_SEPARATOR . SeleniumSetup::DEFAULT_LOCK_FILENAME;
            if (!$this->fileSystem->isFile($lockFilePath)) {
                $this->fileSystem->createFile($lockFilePath);
            }

            $lockFileContents = $this->fileSystem->readFile($lockFilePath);
            $lockFileObj
            if (!empty($lockFileContents)) {

            }

        }

        $this->output->writeln(
            sprintf('Done. Test it at http://%s:%s/wd/hub/', $this->config->getHostname(), $this->config->getPort())
        );
    }
}