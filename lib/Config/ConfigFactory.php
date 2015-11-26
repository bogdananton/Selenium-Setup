<?php
namespace SeleniumSetup\Config;

class ConfigFactory
{
    public static function createFromJSON($jsonString)
    {
        $configOptions = (array)json_decode($jsonString);

        return new Config($configOptions);
    }
}