<?php
namespace SeleniumSetup\Environment;

interface EnvironmentInterface
{
    public function getProjectRootPath();
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