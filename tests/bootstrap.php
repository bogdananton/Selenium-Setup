<?php
$composer = dirname(__FILE__) . '/../vendor/autoload.php';

if (!file_exists($composer)) {
    $composer = dirname(__FILE__) . '/../../../autoload.php';
}

if (!file_exists($composer)) {
    throw new \RuntimeException("Please run 'composer install' first to set up autoloading. $composer");
}

/**
 * @var \Composer\Autoload\ClassLoader $autoloader
 */
$autoloader = include_once $composer;
