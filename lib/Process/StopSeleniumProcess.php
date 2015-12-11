<?php
namespace SeleniumSetup\Process;

use SeleniumSetup\EnvironmentInterface;

class StopSeleniumProcess extends AbstractProcess
{
    /**
     * Find the correct executable to run depending on the OS.
     *
     * @return string
     */
    protected function executable()
    {
        if ($this->env->getOsName() == EnvironmentInterface::OS_WINDOWS) {
            $cmd = 'taskkill /F /IM java.exe';
        } else if ($this->env->getOsName() == EnvironmentInterface::OS_MAC) {
            $cmd = 'pgrep -f "selenium-setup.jar" | xargs kill';
        } else {
            $cmd = 'pgrep -f "selenium-setup.jar" | xargs kill';
        }

        return $cmd;
    }
}