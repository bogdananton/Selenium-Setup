<?php
namespace SeleniumSetup\Process;

use SeleniumSetup\Environment;

interface ProcessInterface
{
    public function __construct(
        array $args = [],
        Environment $env
    );
    public function setArgs(array $args = []);
    public function getArgs();
    public function start();
}