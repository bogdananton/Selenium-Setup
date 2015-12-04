<?php
namespace SeleniumSetupTests;

use Facebook\WebDriver\WebDriverBy;
use SeleniumSetupTests\helpers\BrowserHelper;

class PhantomJSTest extends BrowserHelper
{
    public function setUp()
    {
        $this->startWebDriver(
            getenv('seleniumServerHost'),
            getenv('seleniumServerPort'),
            'phantomjs',
            getEnv('browserProxyHost'),
            getEnv('browserProxyPort')
        );
    }

    /**
     * Page has title.
     */
    public function testPageHasTitle()
    {
        $this->webDriver->get('https://github.com');
        self::assertContains('GitHub', $this->webDriver->getTitle());
    }

    /**
     * Page has 25 items.
     */
    public function testPageHas25Items()
    {
        $this->webDriver->get('https://github.com/trending?l=php');
        self::assertCount(25, $this->webDriver->findElements(WebDriverBy::className('repo-list-item')));
    }
}
