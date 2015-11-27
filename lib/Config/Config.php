<?php
namespace SeleniumSetup\Config;

class Config implements ConfigInterface
{
    protected $hostname;
    protected $port;
    protected $proxyHost;
    protected $proxyPort;
    protected $buildPath;
    protected $logsPath;
    protected $binaries = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        return $this->mapFromArray($config);
    }

    protected function mapFromArray(array $config = [])
    {
        if (empty($config)) {
            return false;
        }

        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return true;
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

    public function setBinary($binaryName, array $binaryInfo)
    {
        $this->binaries[$binaryName] = $binaryInfo;
    }

    public function getBinary($binaryName)
    {
        return isset($this->binaries[$binaryName]) ? $this->binaries[$binaryName] : null;
    }


}