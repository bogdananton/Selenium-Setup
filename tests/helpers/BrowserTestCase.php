<?php
namespace tests\helpers;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class BrowserTestCase extends \PHPUnit_Framework_TestCase
{
    protected $browserName;
    protected $seleniumServerHost;
    protected $seleniumServerPort;
    
    /** @var RemoteWebDriver */
    protected $webDriver;
    
    protected function envSetup($seleniumServerHost, $seleniumServerPort, $browserName)
    {
        $this->setSeleniumServerHost($seleniumServerHost)
             ->setSeleniumServerPort($seleniumServerPort)
             ->setBrowserName($browserName);
        
        $capabilities = [
            WebDriverCapabilityType::BROWSER_NAME => $this->getBrowserName(),
        ];

        $this->webDriver = RemoteWebDriver::create('http://' . $this->getSeleniumServerHost() . ':' . $this->getSeleniumServerPort() . '/wd/hub', $capabilities);

        // Delete all cookies to avoid cart products and scenarios conflicts.
        $this->webDriver->manage()->deleteAllCookies();
    }
    
    public function setUp()
    {
        // Default setup. Override this.
        $this->envSetup('localhost', 81, 'chrome');
    }

    public function tearDown()
    {
        $this->webDriver->quit();
    }

    /**
     * @return mixed
     */
    protected function getBrowserName()
    {
        return $this->browserName;
    }

    /**
     * @param mixed $browserName
     * @return BrowserTestCase
     */
    protected function setBrowserName($browserName)
    {
        $this->browserName = $browserName;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getSeleniumServerHost()
    {
        return $this->seleniumServerHost;
    }

    /**
     * @param mixed $seleniumServerHost
     * @return BrowserTestCase
     */
    protected function setSeleniumServerHost($seleniumServerHost)
    {
        $this->seleniumServerHost = $seleniumServerHost;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getSeleniumServerPort()
    {
        return $this->seleniumServerPort;
    }

    /**
     * @param mixed $seleniumServerPort
     * @return BrowserTestCase
     */
    protected function setSeleniumServerPort($seleniumServerPort)
    {
        $this->seleniumServerPort = $seleniumServerPort;

        return $this;
    }

}