<?php
namespace tests;

use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;

class SampleFirefoxTest extends \PHPUnit_Framework_TestCase
{
    /** @var RemoteWebDriver */
    protected $webDriver;

    /**
     * Get custom window size
     */
    public function testGetCustomWindowSize()
    {
        $capabilities = [WebDriverCapabilityType::BROWSER_NAME => 'firefox'];
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);

        $this->webDriver->manage()->window()->setSize(new WebDriverDimension(640, 900));
        $this->webDriver->get('http://whatsmy.browsersize.com/');
        self::assertEquals(640, $this->webDriver->findElement(WebDriverBy::id('info_ww'))->getText());
    }

    /**
     * Set user agent
     */
    public function testSetUserAgent()
    {
        $useragent = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16';

        // setup
        $capabilities = DesiredCapabilities::firefox();
        /** @var FirefoxProfile $profile */
        $profile = $capabilities->getCapability(FirefoxDriver::PROFILE);
        $profile->setPreference('general.useragent.override', $useragent);

        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);

        // test
        $this->webDriver->get('http://demo.mobiledetect.net/');

        $elements = $this->webDriver->findElements(WebDriverBy::tagName('h1'));

        static::assertEquals(3, count($elements));
        $elementContainingQuestion = $elements[1];

        static::assertEquals('Is your device really a phone?', $elementContainingQuestion->getText());
    }

    public function tearDown()
    {
        $this->webDriver->close();
        $this->webDriver->quit();
    }
}
