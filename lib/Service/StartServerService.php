<?php
namespace SeleniumSetup\Service;

use SeleniumSetup\Binary\Binary;
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
            $writeln[] = '[x] Java is installed.';
            if ($this->env->isJavaVersionDeprecated($javaVersion)) {
                $writeln[] = '[ ] Your Java version needs to be >= 1.6';
                $canInstall = false;
            } else {
                $writeln[] = '[x] Your Java version '. $javaVersion .' seems up to date.';
            }
        }

        if ($this->env->isPHPVersionDeprecated()) {
            $writeln[] = '[ ] Your PHP version '. $this->env->getPHPVersion() .' should be >= 5.3';
            $canInstall = false;
        } else {
            $writeln[] = '[x] Your PHP version is '. $this->env->getPHPVersion();
        }

        if (!$this->env->hasPHPCurlExtInstalled()) {
            $writeln[] = '[ ] cURL extension for PHP is missing.';
            $canInstall = false;
        } else {
            $writeln[] = '[x] cURL '. $this->env->getPHPCurlExtVersion() .' extension is installed.';
        }

        if (!$this->env->hasPHPOpenSSLExtInstalled()) {
            $writeln[] = '[ ] OpenSSL extension for PHP is missing.';
            $canInstall = false;
        } else {
            $writeln[] = '[x] '. $this->env->getPHPOpenSSLExtVersion() .' extension is installed.';
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
        // Create the logs folder.
        if (!$this->system->isDir($this->config->getLogsPath())) {
            $this->system->createDir($this->config->getLogsPath());
        }
    }

    // @todo here
    public function downloadDrivers()
    {
        foreach ($this->config->getBinaries() as $binary) {
            if (!$this->system->isFile($this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName())) {
                $this->output->writeln(sprintf(
                    'Downloading %s %s ...', $binary->getLabel(), $binary->getVersion()
                ));
                $this->system->download(
                    $binary->getDownloadUrl(),
                    $this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName()
                );
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
            $this->runSeleniumServer();
            return true;
        } else {
            $this->output->writeln('Missing required components. Please review your setup.');
            return false;
        }
    }

    protected function runSeleniumServer()
    {
        $commandString = $this->getSeleniumServerCommand();

        if ($commandString === '') {
            return false;
        }

        // make sure that there is no Selenium Server instance running on the same host/port
        $this->stopSeleniumServer();

        $this->output->writeln('==> Selenium Server starting... ');

        $this->output->writeln(sprintf(
            'Start tests: seleniumServerHost=%s seleniumServerPort=%s phpunit',
            $this->config->getHostname(),
            $this->config->getPort()
        ));

        // will run asynchronously with no console output
        $this->system->execCommand($commandString, true);

        return true;
    }

    protected function getSeleniumServerCommand()
    {
        switch ($this->env->getOsName()) {
            case 'windows':
                $commandStringRoot = 'win/start_selenium.bat';
                break;

            case 'linux':
                $commandStringRoot = 'linux/start_selenium.sh';
                break;

            default:
                $this->output->writeln('Start the Selenium Server manually...');
                return '';
                break;
        }

        return sprintf(
            '%s%s %s %s %s %s &',
            $this->config->getCommandsPath(),
            $commandStringRoot,
            $this->config->getHostname(),
            $this->config->getPort(),
            $this->config->getProxyHost(),
            $this->config->getProxyPort()
        );
    }

    protected function stopSeleniumServer()
    {
        // @todo search for the process and terminate it
    }
}