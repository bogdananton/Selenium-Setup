<?php
namespace SeleniumSetup\Config;

use SeleniumSetup\Binary\Binary;
use SeleniumSetup\FileSystem;
use SeleniumSetup\SeleniumSetup;

class ConfigFactory
{
    public static function createFromConfigFile($configFilePath = null)
    {
        $fileSystem = new FileSystem();
        
        if (empty($configFilePath)) {
            $configFilePath = SeleniumSetup::$APP_CONF_PATH . DIRECTORY_SEPARATOR . Config::DEFAULT_CONFIGURATION_FILENAME;
        }
        $configContents = $fileSystem->readFile($configFilePath);
        $configObj = json_decode($configContents);
        
        // @todo: Validate config.
        
        $config = new Config();

        // Normalize the paths.
        $buildPath = realpath(str_replace('{$APP_ROOT_PATH}', SeleniumSetup::$APP_ROOT_PATH, $configObj->buildPath));
        $tmpPath = realpath(str_replace('{$APP_ROOT_PATH}', SeleniumSetup::$APP_ROOT_PATH, $configObj->tmpPath));
        $logsPath = realpath(str_replace('{$APP_ROOT_PATH}', SeleniumSetup::$APP_ROOT_PATH,$configObj->logsPath));

        $config
            ->setName($configObj->name)
            ->setHostname($configObj->hostname)
            ->setPort($configObj->port)
            ->setProxyHost($configObj->proxyHost)
            ->setProxyPort($configObj->proxyPort)
            // Set absolute paths (needed for issuing CLI commands).
            ->setBuildPath($buildPath)
            ->setTmpPath($tmpPath)
            ->setLogsPath($logsPath)
            ->setFilePath($configFilePath);

        foreach ($configObj->binaries as $binaryId => $binaryInfo) {
            $binary = Binary::createFromObject($binaryInfo);
            $config->setBinary($binaryId, $binary);
        }

        return $config;
    }
}