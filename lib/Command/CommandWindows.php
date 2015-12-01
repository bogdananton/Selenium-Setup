<?php
namespace SeleniumSetup\Command;

use SeleniumSetup\Config\ConfigInterface;
use SeleniumSetup\System\System;

class CommandWindows implements CommandInterface
{
    protected $config;
    protected $system;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->system = new System();
    }

    public function invalidateEnvProxy()
    {
        //$this->system->execCommand('SET HTTP_PROXY=');
        //$this->system->execCommand('SET HTTPS_PROXY=');
        putenv('HTTP_PROXY');
        putenv('HTTPS_PROXY');
    }

    public function addBuildFolderToPath()
    {
        //$this->system->execCommand('SET PATH=%PATH%;%cd%/build');
        putenv('PATH=' . getenv('PATH') . ';' . $this->config->getBuildPath());
    }

    public function startSeleniumServer()
    {
        $cmd = 'start /b java -jar ' . $this->config->getBuildPath() . 'selenium-server.jar' .
            ' -port ' . $this->config->getPort() .
            ' -Dhttp.proxyHost=' . $this->config->getProxyHost() .
            ' -Dhttp.proxyPort=' . $this->config->getProxyPort() .
            ' -log ' . $this->config->getLogsPath() . 'selenium.log';
        $this->system->execCommand($cmd);
    }

    public function waitForSeleniumServerToStart()
    {
        sleep(5);
        return true;
    }

    public function stopSeleniumServer()
    {
        $this->system->execCommand('taskkill /F /IM java.exe');
    }

    public function startTests($configPath = null, $testSuite = null)
    {
        putenv('seleniumServerHost='. $this->config->getHostname());
        putenv('seleniumServerPort='. $this->config->getPort());

        $this->system->execCommand('php '. $this->config->getBuildPath() .'phpunit.phar -c '.$configPath.' --testsuite "'. $testSuite .'"', true);
    }

    public function makeFileExecutable($filePath)
    {}

    public function startDisplay()
    {}
}