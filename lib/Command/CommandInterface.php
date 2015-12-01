<?php
namespace SeleniumSetup\Command;

use SeleniumSetup\Config\ConfigInterface;

interface CommandInterface
{
    public function __construct(ConfigInterface $config);
    public function invalidateEnvProxy();
    public function addBuildFolderToPath();
    public function startSeleniumServer();
    public function waitForSeleniumServerToStart();
    public function stopSeleniumServer();
    public function startTests($configPath = null, $testSuite = null);
    public function makeFileExecutable($filePath);
    public function startDisplay();
}