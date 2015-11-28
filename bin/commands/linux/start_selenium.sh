#!/usr/bin/env bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# $1 selenium.server.host
# $2 selenium.server.port
# $3 proxyHost
# $4 proxyPort

if [ -n "$3" ]
    then
        echo "Specific proxy given, invalidate system proxies ..."
        export http_proxy=""
        export https_proxy=""
fi

echo "Include $PWD build in the global PATH ..."
echo "Note: this is needed for the web drivers be found by Selenium."
export PATH=$PATH:$PWD/build

# terminate possible already-running Selenium Server instance
$DIR/stop_selenium.sh

echo "Starting Selenium standalone server ..."
java -jar build/selenium-server.jar -port "$2" -Dhttp.proxyHost="$3" -Dhttp.proxyPort="$4" -Dwebdriver.chrome.driver="./build/chromedriver" -log build/logs/selenium.log &
until $(echo | nc localhost $2); do sleep 1; echo Waiting for Selenium Server to start ...; done;
