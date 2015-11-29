@echo off

REM self_test.bat localhost 81 my.proxy.local 8080

REM %1 selenium.server.host
REM %2 selenium.server.port
REM %3 proxyHost
REM %4 proxyPort

call %~dp0start_selenium.bat %1 %2 %3 %4
call %~dp0start_phpunit_tests.bat
call %~dp0stop_selenium.bat
