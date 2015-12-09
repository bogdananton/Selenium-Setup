<?php
namespace SeleniumSetup\Config;

use SeleniumSetup\Binary\Binary;
use SeleniumSetup\Environment\Environment;
use SeleniumSetup\System\FileSystem;

class ConfigFactory
{
    const DEFAULT_CONFIGURATION_FILE = 'selenium-setup.json';

    public static function createFromConfigFile($configFilePath)
    {
        $system = new FileSystem();
        $env = new Environment();

        $rootPath = $env->getProjectRootPath();
        $configObj = $system->loadJsonFile($configFilePath);

        $config = new Config();
        self::checkSourceIntegrity($config, $configObj);

        $config
            ->setName($configObj->name)
            ->setHostname($configObj->hostname)
            ->setPort($configObj->port)
            ->setProxyHost($configObj->proxyHost)
            ->setProxyPort($configObj->proxyPort)
            // Set absolute paths (needed for issuing CLI commands).
            ->setBuildPath($rootPath . DIRECTORY_SEPARATOR . $configObj->buildPath)
            ->setTmpPath($rootPath . DIRECTORY_SEPARATOR . $configObj->tmpPath)
            ->setLogsPath($rootPath . DIRECTORY_SEPARATOR . $configObj->logsPath);

        foreach ($configObj->binaries as $binaryId => $binaryInfo) {
            $binary = Binary::createFromObject($binaryInfo);
            $config->setBinary($binaryId, $binary);
        }

        return $config;
    }

    protected static function checkSourceIntegrity(Config $configObj, $configSource)
    {
        foreach ($configObj::getAllProperties() as $propertyName) {
            if (!isset($configSource->$propertyName)) {
                throw new \InvalidArgumentException(
                    sprintf('The required configuration key %s is missing.', $propertyName)
                );
            }
        }
    }
}