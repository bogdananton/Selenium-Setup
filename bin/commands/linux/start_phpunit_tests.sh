#!/usr/bin/env bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo "Starting tests ..."
php build/phpunit.phar -c $DIR/phpunit.xml --testsuite "unix"

echo "Stopping previous processes ..."
pgrep -f 'selenium-server.jar' | xargs kill