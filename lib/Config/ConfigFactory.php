<?php
namespace SeleniumSetup\Config;

use SeleniumSetup\Binary\Binary;

class ConfigFactory
{
    public static function createFromJSON($jsonString)
    {
        $configObj = json_decode($jsonString);

        $config = new Config();
        foreach (Config::getAllProperties() as $propertyName) {
            if (!isset($configObj->$propertyName)) {
                throw new \InvalidArgumentException(
                    sprintf('The required configuration key %s is missing.', $propertyName)
                );
            }
        }
        $config->setHostname($configObj->hostname)
            ->setPort($configObj->port)
            ->setProxyHost($configObj->proxyHost)
            ->setProxyPort($configObj->proxyPort)
            ->setCommandsPath($configObj->commandsPath)
            ->setBuildPath($configObj->buildPath)
            ->setLogsPath($configObj->logsPath);

        foreach ($configObj->binaries as $binaryId => $binaryInfo) {
            $binary = Binary::createFromObject($binaryInfo);
            $config->setBinary($binaryId, $binary);
        }

        return $config;
    }
}