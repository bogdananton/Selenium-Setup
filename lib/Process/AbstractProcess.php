<?php
namespace SeleniumSetup\Process;

use SeleniumSetup\Config\ConfigInterface;
use SeleniumSetup\Environment;
use SeleniumSetup\SeleniumSetup;
use Symfony\Component\Process\Process;

abstract class AbstractProcess implements ProcessInterface
{
    protected $args;
    protected $env;

    public function __construct(
        array $args = [],
        Environment $env
    ) {
        $this->args = $args;
        $this->env = $env;
    }

    public function setArgs(array $args = [])
    {
        $this->args = $args;
    }

    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Execute the process.
     *
     * @return int
     */
    public function start()
    {
        $cmd = $this->executable();
        if (!is_null($cmd)) {
            $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
            $process->start();
            // $process->getOutput();
            return $process->getPid();
        }
    }

    protected function executable()
    {
        return null;
    }
}
