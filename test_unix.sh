
if [ -n "$3" ]
  then
	echo "Specific proxy given, invalidate system proxies ..."
	export http_proxy=
	export https_proxy=
fi

echo "Include $PWD build in the global PATH ..."
echo "Note: this is needed for the web drivers be found by Selenium."
export PATH=$PATH:$PWD/build

echo "Starting Selenium standalone server ..."
java -jar build/selenium-server.jar -port $2 -Dhttp.proxyHost="$3" -Dhttp.proxyPort="$4" -Dwebdriver.chrome.driver="./build/chromedriver" -log build/logs/selenium.log > /dev/null &

echo "Wait ..."
sleep 5

#echo "Starting tests ..."
php build/phpunit.phar -c phpunit.xml --testsuite "unix"

echo "Stopping previous processes ..."
pgrep -f 'selenium-server.jar' | xargs kill
