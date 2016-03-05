<?php
namespace SeleniumSetup\Locker;

use SeleniumSetup\FileSystem;
use SeleniumSetup\SeleniumSetup;

class Locker
{
    protected $fileSystem;
    /**
     * @var ServerItem[]
     */
    protected $locker = [];

    public function __construct()
    {
        $this->fileSystem = new FileSystem();
    }

    public function openLockFile()
    {
        if (!$this->fileSystem->isFile(SeleniumSetup::$APP_ROOT_PATH . DIRECTORY_SEPARATOR . SeleniumSetup::DEFAULT_LOCK_FILENAME)) {
            return false;
        }

        $contents = $this->fileSystem->readFile(SeleniumSetup::$APP_ROOT_PATH . DIRECTORY_SEPARATOR . SeleniumSetup::DEFAULT_LOCK_FILENAME);
        $lockerRaw = json_decode($contents);
        foreach ($lockerRaw as $serverObj) {
            $this->addServer(ServerItemFactory::createFromObj($serverObj));
        }
        return true;
    }

    public function writeToLockFile()
    {   
        $this->fileSystem->writeToFile(
            SeleniumSetup::$APP_ROOT_PATH . DIRECTORY_SEPARATOR . SeleniumSetup::DEFAULT_LOCK_FILENAME,
            $this->toJson()
        );
        
        return true;
    }

    public function getServer($name)
    {
        if (!isset($this->locker[$name])) {
            throw new \Exception('Unknown server.');
        }

        return $this->locker[$name];
    }

    public function getServers()
    {
        return $this->locker;
    }

    public function addServer(ServerItem $server)
    {
        $this->locker[$server->getName()] = $server;
    }
    
    public function emptyLocker()
    {
        $this->locker = [];
    }
    
    public function toArray()
    {
        $result = [];
        foreach ($this->locker as $server) {
            $result[$server->getName()] = $server->toArray();
        }
        return $result;
    }
    
    public function toJson()
    {
        $result = [];
        foreach ($this->locker as $server) {
            $result[$server->getName()] = $server->toArray();
        }
        
        return json_encode($result, JSON_PRETTY_PRINT);
    }

}