<?php
namespace tests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;

class SamplePhantomJSTest extends \PHPUnit_Framework_TestCase
{
    /** @var RemoteWebDriver */
    protected $webDriver;

    public function setUp()
    {
        $capabilities = [
            WebDriverCapabilityType::BROWSER_NAME => 'phantomjs',
            'phantomjs.page.settings.userAgent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:25.0) Gecko/20100101 Firefox/25.0',
        ];

        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);

        $window = new WebDriverDimension(1024, 768);
        $this->webDriver->manage()->window()->setSize($window);
    }

    protected $url = 'https://github.com';

    public function testGitHubHome()
    {
        $this->webDriver->get($this->url);
        self::assertContains('GitHub', $this->webDriver->getTitle());
    }

    public function testScrapeUsingJqueryInject()
    {
        $this->webDriver->get($this->url);
        $searchBox = $this->webDriver->findElement(WebDriverBy::name('q'));
        $searchBox->sendKeys('Selenium');
        $searchBox->submit();

        $title = $this->webDriver->getTitle();
        static::assertTrue(is_string($title) && strlen(trim($title)) > 0);
    }

    public function tearDown()
    {
        if ($this->webDriver instanceof RemoteWebDriver) {
            $this->webDriver->quit();
        }
    }
}
