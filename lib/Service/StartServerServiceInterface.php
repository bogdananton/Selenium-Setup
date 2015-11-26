<?php
namespace SeleniumSetup\Service;

interface StartServerServiceInterface
{
    public function detectEnv();
    public function scanPreRequisites();
    public function downloadDrivers();
    public function startServer();
}