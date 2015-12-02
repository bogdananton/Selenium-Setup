[![Build Status](https://travis-ci.org/bogdananton/phpunit-selenium-env.svg?branch=master)](https://travis-ci.org/bogdananton/phpunit-selenium-env)

```
  ____            ___
 /\  _`\         /\_ \                  __
 \ \,\L\_\     __\//\ \      __    ___ /\_\  __  __    ___ ___
  \/_\__ \   /'__`\\ \ \   /'__`\/' _ `\/\ \/\ \/\ \ /' __` __`\
    /\ \L\ \/\  __/ \_\ \_/\  __//\ \/\ \ \ \ \ \_\ \/\ \/\ \/\ \
    \ `\____\ \____\/\____\ \____\ \_\ \_\ \_\ \____/\ \_\ \_\ \_\
    \/_____/\/____/\/____/\/____/\/_/\/_/\/_/\/___/  \/_/\/_/\/_/
    Selenium Environment using Facebook's WebDriver
```

## Install

1. `git clone https://github.com/bogdananton/Selenium-Setup.git`
1. `cd Selenium-Setup`
1. Download [composer.phar](https://getcomposer.org/composer.phar)
1. Run `php composer.phar install`

## Running

1. `php bin/selenium-setup.php start`

## Running `selfTest`

1. `php bin/selenium-setup.php selfTest`

## System Requirements

* Java JRE/JDK (1.6+)
* PHP (5.3+) with curl and open_ssl
* Browser: Chrome, Firefox, IE (only on Windows)

## Environment components:

* [Composer](https://getcomposer.org/)
* [Selenium](http://www.seleniumhq.org) Standalone server
* [Facebook PHP WebDriver](https://github.com/facebook/php-webdriver)
* WebDrivers
   * [ChromeDriver](https://code.google.com/p/selenium/wiki/ChromeDriver)
   * [FirefoxDriver](https://code.google.com/p/selenium/wiki/FirefoxDriver)
   * [IEDriver](https://code.google.com/p/selenium/wiki/InternetExplorerDriver)
* You need to have Chrome, Firefox or IE installed.