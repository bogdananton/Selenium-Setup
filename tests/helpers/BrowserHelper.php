<?php
namespace SeleniumSetupTests\helpers;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class BrowserHelper extends \PHPUnit_Framework_TestCase
{
    /** @var RemoteWebDriver */
    protected $webDriver;

    // Default setup. Override this when needed.
    public function setUp()
    {
        $this->startWebDriver(
            getenv('seleniumServerHost'),
            getenv('seleniumServerPort'),
            getenv('browserName'),
            getEnv('browserProxyHost'),
            getEnv('browserProxyPort')
        );
    }

    public function tearDown()
    {
        if ($this->webDriver instanceof RemoteWebDriver) {
            $this->webDriver->quit();
        }
    }

    protected function startWebDriver(
        $seleniumServerHost,
        $seleniumServerPort,
        $browserName,
        $browserProxyHost = null,
        $browserProxyPort = null,
        $browserCapabilities = []
    ) {
        if (empty($seleniumServerHost)) {
            throw new \RuntimeException('Please declare a valid Selenium Server host.');
        }

        if (empty($seleniumServerPort)) {
            throw new \RuntimeException('Please declare a valid Selenium Server host.');
        }
        
        if (empty($browserName)) {
            throw new \RuntimeException('Please declare a valid browser.');
        }
        
        $defaultBrowserCapabilities = [
            WebDriverCapabilityType::BROWSER_NAME => $browserName,
        ];
        
        if (empty($browserCapabilities)) {
            if (!empty($browserProxyHost) && !empty($browserProxyPort)) {
                $browserCapabilities[WebDriverCapabilityType::PROXY] = array(
                    'proxyType' => 'manual',
                    'httpProxy' => $browserProxyHost .':'. $browserProxyPort,
                    'sslProxy' => $browserProxyHost .':'. $browserProxyPort,
                );
            }
        }

        $browserCapabilities = array_merge($defaultBrowserCapabilities, $browserCapabilities);

        $this->webDriver = RemoteWebDriver::create(
            sprintf('http://%s:%d/wd/hub', $seleniumServerHost, $seleniumServerPort),
            $browserCapabilities
        );

        // Delete all cookies to avoid cart products and scenarios conflicts.
        $this->webDriver->manage()->deleteAllCookies();
    }
}
