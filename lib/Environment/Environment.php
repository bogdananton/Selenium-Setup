<?php
namespace SeleniumSetup\Environment;

use SeleniumSetup\System\System;

class Environment implements EnvironmentInterface
{
    const OS_WINDOWS = 'windows';
    const OS_LINUX = 'linux';
    const OS_MAC = 'mac';


    protected $system;

    public function __construct()
    {
        $this->system = new System();
    }

    // @todo Fine-tune the Windows and Mac detection if possible.
    public function getOsName()
    {
        if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
            return self::OS_WINDOWS;
        } else if (
            strpos(strtolower(PHP_OS), 'mac') !== false ||
            strpos(strtolower(PHP_OS), 'darwin')
        ) {
            return self::OS_MAC;
        } else {
            // Assume Linux.
            return self::OS_LINUX;
        }
    }

    public function getOsVersion()
    {
        // TODO: Implement getOsVersion() method.
    }

    public function getJavaVersion()
    {
        $javaVersionParagraph = $this->system->execCommand('java -version');
        preg_match('/version "([0-9._]+)"/', $javaVersionParagraph, $javaVersionMatches);
        $javaVersion = isset($javaVersionMatches[1]) ? $javaVersionMatches[1] : null;

        return $javaVersion;
    }

    public function isJavaVersionDeprecated($javaVersion)
    {
        return version_compare($javaVersion, '1.6') < 0;
    }

    public function hasJavaCli()
    {
        // TODO: Implement hasJavaCli() method.
    }

    public function hasPHPInstalled()
    {
        // TODO: Implement hasPHPInstalled() method.
    }

    public function getPHPVersion()
    {
        return PHP_VERSION;
    }

    public function isPHPVersionDeprecated()
    {
        return version_compare($this->getPHPVersion(), '5.3') < 0;
    }

    public function canUseTheLatestPHPUnitVersion()
    {
        return version_compare($this->getPHPVersion(), '5.6') >= 0;
    }

    public function hasPHPCurlExtInstalled()
    {
        return function_exists('curl_version');
    }

    public function getPHPCurlExtVersion()
    {
        return curl_version()['version'];
    }

    public function hasPHPOpenSSLExtInstalled()
    {
        return extension_loaded('openssl');
    }

    public function getPHPOpenSSLExtVersion()
    {
        return OPENSSL_VERSION_TEXT;
    }

    public function hasCurlCli()
    {
        return $this->system->execCommand('curl -V');
    }

}