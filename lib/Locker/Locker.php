<?php
namespace SeleniumSetup\Locker;

use SeleniumSetup\FileSystem;
use SeleniumSetup\SeleniumSetup;

class Locker
{
    protected $fileSystem;
    protected $locker;

    public function __construct()
    {
        $this->fileSystem = new FileSystem();
    }

    protected function open()
    {
        $contents = $this->fileSystem->readFile(SeleniumSetup::$APP_ROOT_PATH . DIRECTORY_SEPARATOR . SeleniumSetup::DEFAULT_LOCK_FILENAME);
        $lockerRaw =  json_decode($contents);
        
    }

    protected function write()
    {

    }

    public function getServer($name)
    {
        if (!isset($this->locker[$name])) {
            throw new \Exception('Unknown server.');
        }

        return new ServerItem();
    }

    public function addServer($name, $pid, $port, $configFilePath)
    {

    }

}