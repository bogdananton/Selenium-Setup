<?php
namespace SeleniumSetup\Config;

/**
 * Interface SeleniumServerConfigInterface
 * @package SeleniumSetup\Config
 * Implement specifications from http://www.seleniumhq.org/docs/05_selenium_rc.jsp#server-options
 */
interface SeleniumServer
{
    public function setHostname($hostname);
    public function getHostname();
    public function setPort($port);
    public function getPort();
    public function setProxyHost($proxyHost);
    public function getProxyHost();
    public function setProxyPort($proxyPort);
    public function getProxyPort();
    public function setLogPath($logPath);
    public function getLogPath();
}