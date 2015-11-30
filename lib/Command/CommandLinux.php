<?php
namespace SeleniumSetup\Command;

class CommandLinux extends CommandWindows
{
    public function startSeleniumServer()
    {
        $cmd = 'java -jar ' . $this->config->getBuildPath() . 'selenium-server.jar' .
            ' -port ' . $this->config->getPort() .
            ' -Dhttp.proxyHost=' . $this->config->getProxyHost() .
            ' -Dhttp.proxyPort=' . $this->config->getProxyPort() .
            ' -log ' . $this->config->getLogsPath() . 'selenium.log >/dev/null 2>&1 &';
        $this->system->execCommand($cmd, true);
    }

    public function stopSeleniumServer()
    {
        $this->system->execCommand('pgrep -f "selenium-server.jar" | xargs kill', true);
    }

    public function waitForSeleniumServerToStart()
    {
        $this->system->execCommand('until $(echo | nc '.$this->config->getHostname().' '.$this->config->getPort().'); do sleep 1; echo Waiting for Selenium Server to start ...; done;', true);
    }
}