<?php
namespace SeleniumSetup\Command;

class CommandLinux extends CommandWindows
{
    public function addBuildFolderToPath()
    {
        putenv('PATH=' . getenv('PATH') . ':' . $this->config->getBuildPath());
    }

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
        //sleep(5);
    }

    public function makeFileExecutable($filePath)
    {
        $this->system->execCommand('chmod +x '. $filePath);
    }

    public function startDisplay()
    {
        if (!getenv('DISPLAY')) {
            putenv('DISPLAY=:99.0');
            $cmd = '/sbin/start-stop-daemon --start --pidfile /tmp/custom_xvfb_99.pid --make-pidfile --background --exec /usr/bin/Xvfb -- :99 -ac -screen 0 1280x1024x16';
            $this->system->execCommand($cmd, true);
            // var_dump($cmd);
            sleep(3);
       }
    }
}
