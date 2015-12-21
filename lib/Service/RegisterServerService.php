<?php
namespace SeleniumSetup\Service;


use SeleniumSetup\Config\ConfigFactory;
use SeleniumSetup\Locker\ServerItemFactory;

class RegisterServerService extends AbstractService
{
    const LOG_TYPE_INFO = 'info';
    const LOG_TYPE_ERROR = 'error';

    const VALID_INSTANCE = 1;
    const INVALID_NAME = 3;
    const INVALID_PORT = 5;
    // const INVALID_FILENAME = 7; @todo check filename pattern and sanitize or display error

    const SUCCESS_MESSAGE_ADDED = 'Instance %s was added.';
    const ERROR_MESSAGE_NAME = 'Name %s is reserved for another instance.';
    const ERROR_MESSAGE_PORT = 'Port %d is reserved for another instance.';
    // const ERROR_MESSAGE_FILENAME = 'The name %s is not a valid filename.';

    public function test()
    {
        return $this->env->test();
    }

    public function handle()
    {
        $instanceName = $this->input->getArgument('name');
        $instancePort = $this->input->getArgument('port');

        $this->locker->openLockFile();
        $status = $this->validate($instanceName, $instancePort);

        $this->logStatus($status, $instanceName, $instancePort);

        if ($status === self::VALID_INSTANCE){
            $this->register($instanceName, $instancePort);
            $this->log(self::SUCCESS_MESSAGE_ADDED, $instanceName);
        }
    }

    protected function register($name, $port)
    {
        // Find filename by instance name.
        $filename = $this->getInstanceConfigFilename($name);

        // Update configuration to new instance clone.
        $this->config->setName($name);
        $this->config->setPort($port);
        $this->config->setFilePath($filename);

        // Store new filename using the default instance template.
        $this->fileSystem->createFile($filename, $this->config->toJson());

        // Append instance entry in lock-file.
        $this->locker->addServer(
            ServerItemFactory::createFromProperties(
                $name,
                0,
                $port,
                $filename
            )
        );

        // Write lock-file settings.
        $this->locker->writeToLockFile();
    }

    protected function getInstanceConfigFilename($name)
    {
        return dirname($this->config->getFilePath()) . DIRECTORY_SEPARATOR . $name . '.json';
    }

    /**
     * - initialize OK status.
     *
     * - for each registered instances
     *     - if equal name as the new instance, append (multiply) the value of INVALID_NAME
     *     - if equal port as the new instance, append (multiply) the value of INVALID_PORT
     *
     * - return status final value
     *
     * @return int
     */
    protected function validate($testName, $testPort)
    {
        $status = self::VALID_INSTANCE;

        $instances = $this->locker->getServers();

        foreach ($instances as $instance) {
            if ($instance->getName() == $testName) {
                $status *= self::INVALID_NAME;
            }

            if ($instance->getPort() == $testPort) {
                $status *= self::INVALID_PORT;
            }
        }

        return $status;
    }

    protected function logStatus($status, $name, $port)
    {
        if ($this->isStatusContaining($status, self::INVALID_NAME)) {
            $this->log(self::ERROR_MESSAGE_NAME, $name, self::LOG_TYPE_ERROR);
        }

        if ($this->isStatusContaining($status, self::INVALID_PORT)) {
            $this->log(self::ERROR_MESSAGE_PORT, $port, self::LOG_TYPE_ERROR);
        }
    }

    protected function isStatusContaining($status, $checkedStatus)
    {
        return $status % $checkedStatus === 0;
    }

    protected function log($message, $value = null, $type = self::LOG_TYPE_INFO) {
        $this->output->writeln(sprintf('<' . $type . '>' . $message . '</' . $type . '>', $value));
    }
}