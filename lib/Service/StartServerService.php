<?php
namespace SeleniumSetup\Service;

use SeleniumSetup\Config\ConfigInterface;
use SeleniumSetup\System\System;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartServerService implements StartServerServiceInterface
{
    protected $config;
    protected $input;
    protected $output;
    protected $system;

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
    }

    // @todo Move to protected methods.
    public function detectEnv()
    {
        $canInstall = true;
        $writeln = [];

        $javaVersionParagraph = $this->system->execCommand('java -version');
        preg_match('/version "([0-9._]+)"/', $javaVersionParagraph, $javaVersionMatches);
        $javaVersion = isset($javaVersionMatches[1]) ? $javaVersionMatches[1] : null;

        if (empty($javaVersion)) {
            $writeln[] = '[ ] Java is not installed.';
            $canInstall = false;
        } else {
            $writeln[] = '[x] Java is installed.';
            $javaVersionCheck = version_compare($javaVersion, '1.6') >= 0;
            if (!$javaVersionCheck) {
                $writeln[] = '[ ] Your Java version needs to be >= 1.6';
                $canInstall = false;
            } else {
                $writeln[] = '[x] Your Java version '. $javaVersion .' seems up to date.';
            }
        }


        $phpVersionCheck = version_compare(PHP_VERSION, '5.3') >= 0;
        if (!$phpVersionCheck) {
            $writeln[] = '[ ] Your PHP version '. PHP_VERSION .' should be >= 5.3';
            $canInstall = false;
        } else {
            $writeln[] = '[x] Your PHP version is '. PHP_VERSION;
        }

        $canUseLatestPHPUnit = version_compare(PHP_VERSION, '5.6') >= 0;

        $curlExtIsEnabled = function_exists('curl_version');
        if (!$curlExtIsEnabled) {
            $writeln[] = '[ ] cURL extension for PHP is missing.';
            $canInstall = false;
        } else {
            $curlVersion = curl_version()['version'];
            $writeln[] = '[x] cURL '. $curlVersion .' extension is installed.';
        }

        $curlEnvCheck = $this->system->execCommand('curl -V');

        $sslExtCheck = extension_loaded('openssl');
        if (!$sslExtCheck) {
            $writeln[] = '[ ] OpenSSL extension for PHP is missing.';
            $canInstall = false;
        } else {
            $writeln[] = '[x] '. OPENSSL_VERSION_TEXT .' extension is installed.';
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
        var_dump($this->config);
    }

    public function startServer()
    {
        if ($this->detectEnv()) {
            $this->output->writeln('Everything good, let\'s roll ...');
            $this->prepareEnv();
            $this->downloadDrivers();
        }
    }

}