<?php
namespace SeleniumSetup;

use Symfony\Component\Console\Output\OutputInterface;

class Environment implements EnvironmentInterface
{
    const OS_WINDOWS = 'windows';
    const OS_LINUX = 'linux';
    const OS_MAC = 'mac';

    const OS_TYPE_64BIT = '64bit';
    const OS_TYPE_32BIT = '32bit';

    // @todo Move to public methods into SeleniumSetup\Environment.
    public function test(OutputInterface $output)
    {
        // Pre-requisites.
        $canInstall = true;
        $writeln = [];

        // Start checking.

        $javaVersion = $this->getJavaVersion();

        if (empty($javaVersion)) {
            $writeln[] = '<error>[ ] Java is not installed.</error>';
            $canInstall = false;
        } else {
            $writeln[] = '<info>[x] Java is installed.</info>';
            if ($this->isJavaVersionDeprecated($javaVersion)) {
                $writeln[] = '<error>[ ] Your Java version needs to be >= 1.6</error>';
                $canInstall = false;
            } else {
                $writeln[] = '<info>[x] Your Java version '. $javaVersion .' seems up to date.</info>';
            }
        }

        if ($this->isPHPVersionDeprecated()) {
            $writeln[] = '<error>[ ] Your PHP version '. $this->getPHPVersion() .' should be >= 5.3</error>';
            $canInstall = false;
        } else {
            $writeln[] = '<info>[x] Your PHP version is '. $this->getPHPVersion() .'</info>';
        }

        if (!$this->hasPHPCurlExtInstalled()) {
            $writeln[] = '<error>[ ] cURL extension for PHP is missing.</error>';
            $canInstall = false;
        } else {
            $writeln[] = '<info>[x] cURL '. $this->getPHPCurlExtVersion() .' extension is installed.</info>';
        }

        if (!$this->hasPHPOpenSSLExtInstalled()) {
            $writeln[] = '<error>[ ] OpenSSL extension for PHP is missing.</error>';
            $canInstall = false;
        } else {
            $writeln[] = '<info>[x] '. $this->getPHPOpenSSLExtVersion() .' extension is installed.</info>';
        }

        $output->writeln($writeln);

        return $canInstall;
    }

    public function getProjectRootPath()
    {
        // when running as a phar, use different path
        $pharPath = \Phar::running(false);

        if ($pharPath !== '') {
            return dirname($pharPath);
        }

        return realpath(dirname(__FILE__) . '/../../');
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

    public function getOsType()
    {
        //$type = php_uname('m');
        if (strlen(decbin(~0)) == 64) {
            return self::OS_TYPE_64BIT;
        } else {
            return self::OS_TYPE_32BIT;
        }
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