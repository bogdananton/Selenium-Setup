@echo off

REM @todo check / wait for Selenium Server to start

echo Starting tests ...
php build/phpunit.phar -c %~dp0..\..\..\phpunit.xml --testsuite "windows"