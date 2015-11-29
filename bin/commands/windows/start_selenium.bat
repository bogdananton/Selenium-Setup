@echo off

REM %1 selenium.server.host
REM %2 selenium.server.port
REM %3 proxyHost
REM %4 proxyPort

# terminate possible already-running Selenium Server instance
call %~dp0stop_selenium.bat

IF NOT "%3" == "" (
    echo Specific proxy given, invalidate system proxies ...
    SET HTTP_PROXY=
    SET HTTPS_PROXY=
)

echo Include %cd%/build in the global PATH ...
echo Note: this is needed for the web drivers be found by Selenium.
SET PATH=%PATH%;%cd%/build

echo Starting Selenium standalone server ...
@start /b "" java -jar %~dp0..\..\..\build/selenium-server.jar -port %2 -Dhttp.proxyHost=%3 -Dhttp.proxyPort=%4 -log %~dp0..\..\..\build/logs/selenium.log

REM echo Wait ...
REM timeout /T 3