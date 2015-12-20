<?php
namespace SeleniumSetup\Config;

use SeleniumSetup\Binary\Binary;
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

        $buildPath = $configObj->buildPath;
        $tmpPath = $configObj->tmpPath;
        $logsPath = $configObj->logsPath;

        if (!$fileSystem->isPathAbsolute($buildPath)) {
            $buildPath = $rootPath . DIRECTORY_SEPARATOR . $buildPath;
        }

        if (!$fileSystem->isPathAbsolute($tmpPath)) {
            $tmpPath = $rootPath . DIRECTORY_SEPARATOR . $tmpPath;
        }

        if (!$fileSystem->isPathAbsolute($logsPath)) {
            $logsPath = $rootPath . DIRECTORY_SEPARATOR . $logsPath;
        }
        
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