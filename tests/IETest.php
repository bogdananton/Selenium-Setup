<?php
namespace tests;

use Facebook\WebDriver\WebDriverBy;
use tests\helpers\BrowserHelper;
class IETest extends BrowserHelper
{
    public function setUp()
    {
        $this->envSetup(
            getenv('seleniumServerHost'),
            getenv('seleniumServerPort'),
            'internet explorer'
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
