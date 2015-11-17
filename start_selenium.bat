@echo off

REM %1 selenium.mode
REM %2 selenium.server.host
REM %3 selenium.server.port
REM %4 proxyHost
REM %5 proxyPort

IF NOT "%4" == "" (
    echo Specific proxy given, invalidate system proxies ...
    SET HTTP_PROXY=
    SET HTTPS_PROXY=
)

echo Include %cd%/build in the global PATH ...
echo Note: this is needed for the web drivers be found by Selenium.
SET PATH=%PATH%;%cd%/build

echo Starting Selenium standalone server ...
@start /b "" java -jar build/selenium-server.jar -port %3 -Dhttp.proxyHost=%4 -Dhttp.proxyPort=%5 -log build/logs/selenium.log

echo Wait ...
timeout /T 5

REM cls

IF "%1" == "self-test" (
    echo Starting tests ...
    php build/phpunit.phar -c phpunit.xml --testsuite "windows"
    
    echo Stopping previous processes ...
    taskkill /F /IM java.exe
)