<?php
namespace SeleniumSetup\Process;

use SeleniumSetup\EnvironmentInterface;
use SeleniumSetup\SeleniumSetup;
use Symfony\Component\Process\Process;

class StartSeleniumProcess extends AbstractProcess
{
    /**
     * Find the correct executable to run depending on the OS.
     *
     * @return string
     */
    protected function executable()
    {
        if ($this->env->getOsName() == EnvironmentInterface::OS_WINDOWS) {
            $cmd = 'start /b java -jar %s -port %s -Dhttp.proxyHost=%s -Dhttp.proxyPort=%s -log %s';
        } else if ($this->env->getOsName() == EnvironmentInterface::OS_LINUX) {
            $cmd = 'java -jar %s -port %s -Dhttp.proxyHost=%s -Dhttp.proxyPort=%s -log %s >/dev/null 2>&1 &';
        } else {
            $cmd = 'java -jar %s -port %s -Dhttp.proxyHost=%s -Dhttp.proxyPort=%s -log %s >/dev/null 2>&1 &';
        }

        $cmd = vsprintf($cmd, $this->getArgs());

        return $cmd;
    }
}