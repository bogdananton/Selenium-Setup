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
    }

    public function makeFileExecutable($filePath)
    {
        $this->system->execCommand('chmod +x '. $filePath);
    }

    public function startDisplay()
    {
        $cmd = 'if ! xset q &>/dev/null; then ';
            $cmd .= 'export DISPLAY=:99 && ';
            $cmd .= '/sbin/start-stop-daemon --start --quiet --pidfile /tmp/custom_xvfb_99.pid --make-pidfile --background --exec /usr/bin/Xvfb -- :99 -ac -screen 0 1280x1024x16; ';
            $cmd .= 'sleep 3; ';
        $cmd .= 'fi;';

        $this->system->execCommand($cmd, true);
    }
}
