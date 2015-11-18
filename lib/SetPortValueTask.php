<?php
// For the moment, I cannot get the autoloader to work.
// @see https://github.com/phingofficial/phing/blob/master/bin/phing.php#L11
require_once dirname(__FILE__)  . DIRECTORY_SEPARATOR . 'SeleniumPortNode.php';

class SetPortValueTask extends \Task
{
    public function main()
    {
        $newValue = $this->project->getProperty('selenium.server.port');
        $oldValue = $this->getSeleniumPortNode()->getValue();

        switch (true) {
            case (int)$newValue === 0:
                $this->log('Invalid new value: ' . $newValue . ', seen as [' . var_export($newValue, 2) . '].');
                break;

            case ($oldValue == $newValue):
                $this->log('No update was needed.');
                break;

            default:
                $this->getSeleniumPortNode()->setValue($newValue);
                $this->log('Current Selenium host proxy is ' . $oldValue);
                $this->log('Updated ' . self::getPhunitFilename() . ', new value is ' . $newValue);
                break;
        }
    }

    public static function getPhunitFilename()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'phpunit.xml';
    }

    protected function getSeleniumPortNode()
    {
        return SeleniumPortNode::buildSeleniumServerPortFromPhpunitFile();
    }
}