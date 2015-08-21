PHPUnit-Selenium environment starter.

## System Requirements:

* PHP (5.3+) with curl and open_ssl
* Java JRE/JDK (1.6+)
* Browser: Chrome

These components should be installed on your system using your software manager.

> For Windows: you should have the `php` and `java` commands available in your command prompt (cmd/git bash/...) path available in your PATH.


## Environment components:

* Phing
* Selenium Standalone server
* Chromedriver
* Composer
* Facebook WebDriver


### Installation (Linux):

**Phing** (a build tool based on ​Apache Ant)

**Go to the project root**, [download the phar](http://www.phing.info/get/phing-latest.phar), rename to `phing` and make it an executable.

```
wget http://www.phing.info/get/phing-latest.phar -O; mv phing-latest.phar phing; chmod +x phing
```

Run `phing` to do an environment check and start downloading and unpacking.

After phing has finished, run 'java -jar selenium-server-standalone-2.47.0.jar' in a different terminal to start the server, then run `php phpunit.phar` in the current terminal.


### Manual install (without phing):

* Download [Composer phar](https://getcomposer.org/composer.phar)
* Download [PHPUnit phar](https://phar.phpunit.de/phpunit.phar)
* Download [Selenium Standalone Server jar file](http://selenium-release.storage.googleapis.com/2.47/selenium-server-standalone-2.47.0.jar)
* Download [Chromedriver](http://chromedriver.storage.googleapis.com/index.html?path=2.18/) and unzip/place the binary in your environment PATH. (c:\Windows or /usr/bin and chmod +x it)
* Run 'php composer.phar install' to init facebook/webdriver
* Run 'java -jar selenium-server-standalone-2.47.0.jar' in a new terminal.
* Run 'php phpunit.phar' to start the sample test.
