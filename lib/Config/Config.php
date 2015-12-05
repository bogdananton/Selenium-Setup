<?php
namespace SeleniumSetup\Config;

use SeleniumSetup\Binary\Binary;

class Config implements ConfigInterface
{
    protected $name;
    protected $hostname;
    protected $port;
    protected $proxyHost;
    protected $proxyPort;
    protected $buildPath;
    protected $tmpPath;
    protected $logsPath;
    protected $binaries = [];

    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @return array
     */
    public static function getAllProperties()
    {
        return array_keys(get_object_vars(new self));
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Config
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     * @return Config
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return Config
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getProxyHost()
    {
        return $this->proxyHost;
    }

    /**
     * @param string $proxyHost
     * @return Config
     */
    public function setProxyHost($proxyHost)
    {
        $this->proxyHost = $proxyHost;
        return $this;
    }

    /**
     * @return int
     */
    public function getProxyPort()
    {
        return $this->proxyPort;
    }

    /**
     * @param int $proxyPort
     * @return Config
     */
    public function setProxyPort($proxyPort)
    {
        $this->proxyPort = $proxyPort;
        return $this;
    }

    /**
     * @return string
     */
    public function getBuildPath()
    {
        return $this->buildPath;
    }

    /**
     * @param string $buildPath
     * @return Config
     */
    public function setBuildPath($buildPath)
    {
        $this->buildPath = $buildPath;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTmpPath()
    {
        return $this->tmpPath;
    }

    /**
     * @param mixed $tmpPath
     * @return Config
     */
    public function setTmpPath($tmpPath)
    {
        $this->tmpPath = $tmpPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogsPath()
    {
        return $this->logsPath;
    }

    /**
     * @param string $logsPath
     * @return Config
     */
    public function setLogsPath($logsPath)
    {
        $this->logsPath = $logsPath;
        return $this;
    }

    public function setBinaries(array $binaries)
    {
        $this->binaries = $binaries;
    }

    public function getBinaries()
    {
        return $this->binaries;
    }

    public function setBinary($binaryId, Binary $binaryInfo)
    {
        $this->binaries[$binaryId] = $binaryInfo;
    }

    public function getBinary($binaryName)
    {
        return isset($this->binaries[$binaryName]) ? $this->binaries[$binaryName] : null;
    }

    public function jsonSerialize() {
        $object = (object)get_object_vars($this);
        $object->binaries = [];

        /**
         * @var integer $index
         * @var Binary $binary
         */
        foreach ($this->binaries as $index => $binary) {
            $object->binaries[$index] = $binary->jsonSerialize();
        }

        return $object;
    }
}