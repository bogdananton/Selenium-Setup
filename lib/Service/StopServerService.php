<?php
namespace SeleniumSetup\Service;

class StopServerService extends AbstractService
{
    public function handle()
    {
        $this->locker->openLockFile();
        $serverItem = $this->locker->getServer($this->input->getArgument('name'));
        $this->env->killProcessByPid($serverItem->getPid());
    }
}