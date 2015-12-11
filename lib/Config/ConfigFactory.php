<?php
namespace SeleniumSetup\Config;

use SeleniumSetup\Binary;
use SeleniumSetup\Environment;
use SeleniumSetup\FileSystem;

class ConfigFactory
{
    public static function createFromConfigFile($configFilePath = null)
    {
        $fileSystem = new FileSystem();
        
        $rootPath = realpath(dirname(__FILE__) .'/../..');
        
        if (empty($configFilePath)) {
            $configFilePath = $rootPath . DIRECTORY_SEPARATOR . Config::DEFAULT_CONFIGURATION_FILENAME;
        }
        $configContents = $fileSystem->readFile($configFilePath);
        $configObj = json_decode($configContents);
        
        // @todo: Validate config.
        
        $config = new Config();
        
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
}