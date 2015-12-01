<?php
namespace SeleniumSetup\Service;

use SeleniumSetup\Command\CommandFactory;
use SeleniumSetup\Config\ConfigInterface;
use SeleniumSetup\Environment\Environment;
use SeleniumSetup\System\System;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartServerService implements StartServerServiceInterface
{
    protected $config;
    protected $input;
    protected $output;
    protected $system;
    protected $env;
    protected $command;

    public function __construct(
        ConfigInterface $config,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->config = $config;
        $this->input = $input;
        $this->output = $output;
        $this->system = new System();
        $this->env = new Environment();
        $this->command = CommandFactory::create($this->config, $this->env);
    }

    // @todo Move to public methods into SeleniumSetup\Environment.
    public function detectEnv()
    {
        // Pre-requisites.
        $canInstall = true;
        $writeln = [];

        // Start checking.

        $javaVersion = $this->env->getJavaVersion();

        if (empty($javaVersion)) {
            $writeln[] = '[ ] Java is not installed.';
            $canInstall = false;
        } else {
            $writeln[] = '<info>[x] Java is installed.</info>';
            if ($this->env->isJavaVersionDeprecated($javaVersion)) {
                $writeln[] = '[ ] Your Java version needs to be >= 1.6';
                $canInstall = false;
            } else {
                $writeln[] = '<info>[x] Your Java version '. $javaVersion .' seems up to date.</info>';
            }
        }

        if ($this->env->isPHPVersionDeprecated()) {
            $writeln[] = '[ ] Your PHP version '. $this->env->getPHPVersion() .' should be >= 5.3';
            $canInstall = false;
        } else {
            $writeln[] = '<info>[x] Your PHP version is '. $this->env->getPHPVersion() .'</info>';
        }

        if (!$this->env->hasPHPCurlExtInstalled()) {
            $writeln[] = '[ ] cURL extension for PHP is missing.';
            $canInstall = false;
        } else {
            $writeln[] = '<info>[x] cURL '. $this->env->getPHPCurlExtVersion() .' extension is installed.</info>';
        }

        if (!$this->env->hasPHPOpenSSLExtInstalled()) {
            $writeln[] = '[ ] OpenSSL extension for PHP is missing.';
            $canInstall = false;
        } else {
            $writeln[] = '<info>[x] '. $this->env->getPHPOpenSSLExtVersion() .' extension is installed.</info>';
        }

        $this->output->writeln($writeln);

        return $canInstall;
    }

    public function prepareEnv()
    {
        // Create the build folder. (Where the binaries will reside).
        if (!$this->system->isDir(($this->config->getBuildPath()))) {
            $this->system->createDir($this->config->getBuildPath());
        }
        // Create the tmp folder.
        if (!$this->system->isDir(($this->config->getTmpPath()))) {
            $this->system->createDir($this->config->getTmpPath());
            //putenv('TMP='.$this->config->getTmpPath());
            //putenv('TEMP='.$this->config->getTmpPath());
            //chown($this->config->getTmpPath(),666);
        }
        // Create the logs folder.
        if (!$this->system->isDir($this->config->getLogsPath())) {
            $this->system->createDir($this->config->getLogsPath());
        }
    }

    // @todo here
    public function downloadDrivers()
    {
        foreach ($this->config->getBinaries() as $binary) {
            // Skip binaries that don't belong to the current operating system.
            if (
                !empty($binary->getOs()) && $binary->getOs() != $this->env->getOsName() ||
                !empty($binary->getOsType() && $binary->getOsType() != $this->env->getOsType() )
            ) {
                continue;
            }
            if (!$this->system->isFile($this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName())) {
                $this->output->writeln(sprintf(
                    'Downloading %s %s ...', $binary->getLabel(), $binary->getVersion()
                ));
                // Download.
                $downloadTo = $this->config->getBuildPath() . DIRECTORY_SEPARATOR . pathinfo($binary->getDownloadUrl(), PATHINFO_BASENAME);
                $this->system->download(
                    $binary->getDownloadUrl(),
                    $downloadTo
                );
                // Unzip.
                if (in_array(pathinfo($binary->getDownloadUrl(), PATHINFO_EXTENSION), ['zip', 'tar', 'tar.gz'])) {
                    $zip = new \ZipArchive;
                    $res = $zip->open($downloadTo);
                    if ($res === TRUE) {
                        $zip->extractTo(
                            $this->config->getBuildPath(),
                            [$binary->getBinName()]
                        );
                        $zip->close();
                    }
                } else {
                    rename($downloadTo, $this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName());
                }
                // Make executable.
                $this->command->makeFileExecutable($this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName());
            } else {
                $this->output->writeln(sprintf(
                    'Skipping %s %s. Binary already exists.', $binary->getLabel(), $binary->getVersion()
                ));
            }
        }
    }

    public function startServer()
    {
        if ($this->detectEnv()) {
            $this->output->writeln('Everything good, let\'s roll ...');
            $this->prepareEnv();
            $this->downloadDrivers();
            $this->runServer();
            return true;
        } else {
            $this->output->writeln('Missing required components. Please review your setup.');
            return false;
        }
    }

    protected function runServer()
    {
        $this->command->stopSeleniumServer();
        if (!empty($this->config->getProxyHost())) {
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

    public function stopServer()
    {
        $this->command->stopSeleniumServer();
    }

    public function runSelfTest()
    {
        if ($this->startServer()) {
            $this->command->waitForSeleniumServerToStart();
            $this->command->startTests(
                $this->env->getProjectRootPath() . DIRECTORY_SEPARATOR . 'phpunit.xml',
                $this->env->getOsName()
            );
            $this->stopServer();
        }
    }
}