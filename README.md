```
  ____            ___
 /\  _`\         /\_ \                  __
 \ \,\L\_\     __\//\ \      __    ___ /\_\  __  __    ___ ___
  \/_\__ \   /'__`\\ \ \   /'__`\/' _ `\/\ \/\ \/\ \ /' __` __`\
    /\ \L\ \/\  __/ \_\ \_/\  __//\ \/\ \ \ \ \ \_\ \/\ \/\ \/\ \
    \ `\____\ \____\/\____\ \____\ \_\ \_\ \_\ \____/\ \_\ \_\ \_\
    \/_____/\/____/\/____/\/____/\/_/\/_/\/_/\/___/  \/_/\/_/\/_/
    PHPUnit Environment with Facebook's WebDriver
```

## System Requirements:

* PHP (5.3+) with curl and open_ssl
* Java JRE/JDK (1.6+)
* Browser: Chrome, Firefox, IE (only on Windows)

These components should be installed on your system using your software manager.

*On Windows*

You need to have `php`, `java`, `curl` commands available in your command prompt or registered in your PATH.

## Environment components:

* Phing
* Selenium Standalone server
* Chromedriver, IEDriver
* Composer
* Facebook WebDriver

### First run

1. Go to project root
1. Download [phing-latest.phar](http://www.phing.info/get/phing-latest.phar)
1. *Optional* add `phing-latest.phar` to your path
    1. `wget http://www.phing.info/get/phing-latest.phar -O; mv phing-latest.phar phing; chmod +x phing`
1. Run phing: `php phing-latest.phar`
1. After phing has finished, the last command `java -jar selenium-server.jar` will start the Selenium Server.
1. In a different terminal to start the server, then run `php phpunit.phar` in the current terminal to run the current tests.

### Use in your testing project

1. Go to **your** project root
1. Add the package `composer require bogdananton/phpunit-selenium-env`
1. Import the build.xml from `phpunit-selenium-env` task in your project. See the example below.
1. Run phing: `php phing-latest.phar`

Example of `build.xml` in **your** project:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<project name="avangate-integration-tests" default="avangateSetup" basedir="." description="Setup Avangate Integration Tests">
    <resolvepath propertyName="project.path" file="." />
    <import file="vendor/bogdananton/phpunit-selenium-env/build.xml"/>
    <target name="avangateSetup">
        <phingcall target="envSetup" />
        <mkdir dir="${build.path}/logs" />
    </target>
</project>
```