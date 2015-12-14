<?php
namespace SeleniumSetup\Locker;

class ServerItem
{
    const DATE_FORMAT = 'Y-m-d H:i:s';
    
    protected $name;
    protected $dateStarted;
    protected $dateStopped;
    protected $pid;
    protected $port;
    protected $configFilePath;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return ServerItem
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateStarted()
    {
        return $this->dateStarted;
    }

    /**
     * @param mixed $dateStarted
     * @return ServerItem
     */
    public function setDateStarted($dateStarted)
    {
        $this->dateStarted = $dateStarted;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateStopped()
    {
        return $this->dateStopped;
    }

    /**
     * @param mixed $dateStopped
     * @return ServerItem
     */
    public function setDateStopped($dateStopped)
    {
        $this->dateStopped = $dateStopped;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param mixed $pid
     * @return ServerItem
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     * @return ServerItem
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfigFilePath()
    {
        return $this->configFilePath;
    }

    /**
     * @param mixed $configFilePath
     * @return ServerItem
     */
    public function setConfigFilePath($configFilePath)
    {
        $this->configFilePath = $configFilePath;
        return $this;
    }

    public function toArray()
    {
        return (array)get_object_vars($this);
    }
    
}