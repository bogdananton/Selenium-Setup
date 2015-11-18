#!/usr/bin/env bash

# $1 selenium.mode
# $2 selenium.server.host
# $3 selenium.server.port
# $4 proxyHost
# $5 proxyPort

if [ -n "$4" ]
    then
        echo "Specific proxy given, invalidate system proxies ..."
        export http_proxy=
        export https_proxy=
fi

echo "Include $PWD build in the global PATH ..."
echo "Note: this is needed for the web drivers be found by Selenium."
export PATH=$PATH:$PWD/build

echo "Starting Selenium standalone server ..."
java -jar build/selenium-server.jar -port $3 -Dhttp.proxyHost="$4" -Dhttp.proxyPort="$5" -Dwebdriver.chrome.driver="./build/chromedriver" -log build/logs/selenium.log > /dev/null &

echo "Wait ..."
sleep 5

if [ "$1" -eq "self-test" ]
    then

fi