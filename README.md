[![Build Status](https://travis-ci.org/bogdananton/Selenium-Setup.svg)](https://travis-ci.org/bogdananton/Selenium-Setup)

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

## Listing commands

1. `php bin/selenium-setup.php list`

```
Selenium Setup version 4.0.0

Usage:
  command [options] [arguments]

Available commands:
  help      Displays help for a command
  list      Lists commands
  register  Register a SeleniumSetup server instance.
  servers   List registered Selenium Servers.
  start     Start Selenium Server setup with all supported drivers attached to it.
  stop      Stop Selenium Server.
```

## Running (default instance)

1. `php bin/selenium-setup.php start`

```
Usage:
  start [options] [--] [<name>]

Arguments:
  name                   The instance name. [default: "defaultServer"]
```

## Registering instances

1. `php bin/selenium-setup.php register secondInstance 4445`

```
Usage:
  register [options] [--] <name> <port>

Arguments:
  name                   Instance name.
  port                   Instance port.
```

## Stopping an instance

1. `php bin/selenium-setup.php stop secondInstance`

```
Usage:
  stop [<name>]

Arguments:
  name                  The name of the server. [default: "defaultServer"]
```

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

## Demo

[![asciicast](https://asciinema.org/a/5s4dt4szujci9dfcx2fe9qwt4.png)](https://asciinema.org/a/5s4dt4szujci9dfcx2fe9qwt4)
