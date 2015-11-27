<?php
namespace SeleniumSetup\Config;

/**
 * Interface SeleniumServerConfigInterface
 * @package SeleniumSetup\Config
 * Implement specifications from http://www.seleniumhq.org/docs/05_selenium_rc.jsp#server-options
 */
interface ConfigInterface
{
    public function setHostname($hostname);
    public function getHostname();
    public function setPort($port);
    public function getPort();
    public function setProxyHost($proxyHost);
    public function getProxyHost();
    public function setProxyPort($proxyPort);
    public function getProxyPort();
    public function setBuildPath($buildPath);
    public function getBuildPath();
    public function setLogsPath($logPath);
    public function getLogsPath();
    public function setBinaries(array $binaries);
    public function getBinaries();
    public function setBinary($binaryName, array $binaryInfo);
    public function getBinary($binaryName);
}