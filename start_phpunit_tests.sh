#!/usr/bin/env bash

echo "Starting tests ..."
php build/phpunit.phar -c phpunit.xml --testsuite "unix"

echo "Stopping previous processes ..."
pgrep -f 'selenium-server.jar' | xargs kill