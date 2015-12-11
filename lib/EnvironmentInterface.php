<?php
namespace SeleniumSetup;

interface EnvironmentInterface
{
    const OS_WINDOWS = 'windows';
    const OS_LINUX = 'linux';
    const OS_MAC = 'mac';

    const OS_TYPE_64BIT = '64bit';
    const OS_TYPE_32BIT = '32bit';
    
    public function getOsName();
    public function getOsVersion();
    public function getOsType();
    public function getJavaVersion();
    public function isJavaVersionDeprecated($javaVersion);
    public function hasJavaCli();
    public function hasPHPInstalled();
    public function getPHPVersion();
    public function isPHPVersionDeprecated();
    public function hasPHPCurlExtInstalled();
    public function getPHPCurlExtVersion();
    public function hasPHPOpenSSLExtInstalled();
    public function getPHPOpenSSLExtVersion();
    public function hasCurlCli();
}