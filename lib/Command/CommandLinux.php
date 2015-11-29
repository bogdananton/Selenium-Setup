<?php
namespace SeleniumSetup\Command;

use SeleniumSetup\Config\ConfigInterface;
use SeleniumSetup\System\System;

class CommandLinux extends CommandWindows
{
    public function startSeleniumServer()
    {
        $cmd = 'java -jar ' . $this->config->getBuildPath() . 'selenium-server.jar' .
            ' -port ' . $this->config->getPort() .
            ' -Dhttp.proxyHost=' . $this->config->getProxyHost() .
            ' -Dhttp.proxyPort=' . $this->config->getProxyPort() .
            ' -log ' . $this->config->getLogsPath() . 'selenium.log &';
        $this->system->execCommand($cmd);
    }
}