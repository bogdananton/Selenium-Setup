<?php
namespace SeleniumSetup\Config;

use SeleniumSetup\Binary\Binary;
use SeleniumSetup\System\System;

class ConfigFactory
{
    public static function createFromConfigFile($configFilePath)
    {
        $realPath = realpath($configFilePath);
        $rootPath = pathinfo($realPath, PATHINFO_DIRNAME);

        $system = new System();
        $jsonString = $system->readFile($configFilePath);
        $configObj = json_decode($jsonString);

        $config = new Config();
        foreach (Config::getAllProperties() as $propertyName) {
            if (!isset($configObj->$propertyName)) {
                throw new \InvalidArgumentException(
                    sprintf('The required configuration key %s is missing.', $propertyName)
                );
            }
        }
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