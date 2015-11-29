<?php
namespace SeleniumSetup\Command;

use SeleniumSetup\Config\ConfigInterface;
use SeleniumSetup\Environment\Environment;

class CommandFactory
{
    /**
     * @param ConfigInterface $config
     * @param Environment $env
     * @return FALSE|CommandWindows
     */
    public static function create(ConfigInterface $config, Environment $env)
    {
        switch ($env->getOsName()) {
            case Environment::OS_WINDOWS:
                return new CommandWindows($config);
                break;

            case Environment::OS_LINUX:
                return false;
                break;

            default:
                return false;
                break;
        }
    }
}