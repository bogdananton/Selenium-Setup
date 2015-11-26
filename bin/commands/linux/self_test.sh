DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

sh $DIR/start_selenium.sh $1 $2 $3 $4
sh $DIR/start_phpunit_tests.sh
