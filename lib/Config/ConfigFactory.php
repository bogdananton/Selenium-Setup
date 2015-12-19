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
        
        $config
            ->setName($configObj->name)
            ->setHostname($configObj->hostname)
            ->setPort($configObj->port)
            ->setProxyHost($configObj->proxyHost)
            ->setProxyPort($configObj->proxyPort)
            // Set absolute paths (needed for issuing CLI commands).
            ->setBuildPath($rootPath . DIRECTORY_SEPARATOR . $configObj->buildPath)
            ->setTmpPath($rootPath . DIRECTORY_SEPARATOR . $configObj->tmpPath)
            ->setLogsPath($rootPath . DIRECTORY_SEPARATOR . $configObj->logsPath)
            ->setFilePath($configFilePath);

        foreach ($configObj->binaries as $binaryId => $binaryInfo) {
            $binary = Binary::createFromObject($binaryInfo);
            $config->setBinary($binaryId, $binary);
        }

        return $config;
    }

    public static function createJSONFromConfigFile(ConfigInterface $config)
    {
        $response = new \stdClass();
        $response->name = $config->getName();
        $response->hostname = $config->getHostname();
        $response->port = $config->getPort();
        $response->proxyHost = $config->getProxyHost();
        $response->proxyPort = $config->getProxyPort();
        $response->buildPath = $config->getBuildPath();
        $response->tmpPath = $config->getTmpPath();
        $response->logsPath = $config->getLogsPath();
        $response->filePath = $config->getFilePath();

        $response->binaries = [];

        /** @var Binary $binary */
        foreach ($config->getBinaries() as $binaryId => $binary) {
            $response->binaries[$binaryId] = $binary->toArray();
        }

        return $response;
    }
}