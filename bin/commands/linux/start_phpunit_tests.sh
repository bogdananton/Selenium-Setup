#!/usr/bin/env bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Check and wait for Selenium Server to be running
checkCommand=`ps aux | grep selenium-server.jar | grep 4444 | grep java | wc -l`

while [ "$checkCommand" == "0" ];
do
    echo "Loading..."
    sleep 1
    checkCommand=`ps aux | grep selenium-server.jar | grep 4444 | grep java | wc -l`
done

if [ "$testsuite" == "" ]
    then
        testsuite="unix"
fi

echo "Starting tests ..."
RUN_TESTS=`php build/phpunit.phar -c $DIR/../../../phpunit.xml --testsuite $testsuite`
echo $RUN_TESTS

$DIR/stop_selenium.sh

# exit with error code
if [[ $RUN_TESTS = *"FAILURES"* ]]
then
    exit 2
fi
