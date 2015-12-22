<?php
namespace SeleniumSetup;

use GuzzleHttp\Client;
use GuzzleHttp\Event\ProgressEvent;
use SeleniumSetup\Config\ConfigInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Environment
{
    protected $config;
    protected $fileSystem;
    protected $env;
    protected $input;
    protected $output;

    const OS_WINDOWS = 'windows';
    const OS_LINUX = 'linux';
    const OS_MAC = 'mac';
    const OS_TYPE_64BIT = '64bit';
    const OS_TYPE_32BIT = '32bit';

    public function __construct(
        ConfigInterface $config,
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->config = $config;
        $this->fileSystem = new FileSystem();
        $this->input = $input;
        $this->output = $output;
    }

    // @todo Move to public methods into SeleniumSetup\Environment.
    public function test()
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

        $this->output->writeln($writeln);

        return $canInstall;
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

    public function isWindows()
    {
        return $this->getOsName() == self::OS_WINDOWS;
    }

    public function isMac()
    {
        return $this->getOsName() == self::OS_MAC;
    }

    public function isLinux()
    {
        return $this->getOsName() == self::OS_LINUX;
    }
    
    public function isAdmin()
    {
        if ($this->isWindows()) {
            $cmd = 'NET SESSION';
            $lookForNegative = '^System error';

        } else {
            $cmd ='sudo -n true';
            $lookForNegative = '^sudo\: a password is required';
        }

        $output = new BufferedOutput();

        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
        
        return !(preg_match('/'. $lookForNegative .'/is', $output->fetch()));
    }

    public function getJavaVersion()
    {
        $cmd = 'java -version';

        $output = new BufferedOutput();

        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
        
        preg_match('/version "([0-9._]+)"/', $output->fetch(), $javaVersionMatches);
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

    // @todo Decide if this is still neded.
    public function hasCurlCli()
    {
        //$command = new GetCurlVersionCommand();
        //$commandInput = new ArrayInput([]);
        //$commandOutput = new BufferedOutput();
        //$returnCode = $command->run($commandInput, $commandOutput);

        //return preg_match('/^curl ([0-9._]+)/', $commandOutput->fetch());
    }
    
    public function getEnvVar($varName)
    {
        return getenv($varName);
    }
    
    public function setEnvVar($varName, $varValue = '')
    {
        if ($varValue != '') {
            putenv($varName. '=' .$varValue);
        } else {
            putenv($varName);
        }
    }

    public function addPathToGlobalPath($path)
    {
        if ($this->isWindows()) {
            $separator = ';';
        } else {
            $separator = ':';
        }

        putenv('PATH=' . getenv('PATH') . $separator . $path);
        $this->output->writeln(sprintf('Added %s to global path.', $path));
    }

    public function download($from, $to)
    {
        $client = new Client();
        $client->setDefaultOption('verify', SeleniumSetup::$APP_ROOT_PATH . DIRECTORY_SEPARATOR . SeleniumSetup::SSL_CERT_FILENAME);
        $request = $client->createRequest('GET', $from, ['save_to'=> $to]);

        $computeRemainingSize = function(ProgressEvent $e) {
            if ($e->downloaded <= 0) {
                return 0;
            }
            $remainingSize = $e->downloadSize - $e->downloaded;
            if ($remainingSize > 0) {
                return round($e->downloaded / $e->downloadSize, 2) * 100;
            } else {
                return 100;
            }
        };

        // $progress = new ProgressBar($output, 5);
        // $progress->start();
        $output = new BufferedOutput();

        $request->getEmitter()->on('progress', function (ProgressEvent $e) use ($computeRemainingSize, $output) {

            $output->write(
                sprintf("Downloaded %s%%\r", $computeRemainingSize($e))
            );

            //$a = $computeRemainingSize($e);
            //if ($a == 100) {
            //    $progress->finish();
            //} else {
            //    if ($a % 10 == 0) {
            //        $progress->advance();
            //    }
            //}
        });

        $client->send($request);

        return $output->fetch();
    }

    public function getCurlVersion()
    {
        $cmd = 'curl -V';

        $output = $this->output;

        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }
    
    public function killProcessByPid($pid)
    {
        if ($this->isWindows()) {
            $cmd = 'taskkill /F /PID %d';
        } else {
            $cmd = 'kill -9 %d';
        }

        $cmd = sprintf($cmd, $pid);

        $output = $this->output;

        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }

    public function killProcessByName($processName)
    {
        if ($this->isWindows()) {
            $cmd = 'taskkill /F /IM %s';
        } else {
            $cmd = 'pgrep -f "%s" | xargs kill';
        }

        $cmd = sprintf($cmd, $processName);

        $output = $this->output;

        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }

    public function listenToPort($port)
    {
        if ($this->isWindows()) {
            $cmd = 'netstat -ano|findstr :%d';
        } else {
            $cmd = 'netstat -tulpn | grep :%d';
        }

        $cmd = sprintf($cmd, $port);

        $output = new BufferedOutput();

        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
        
        return $output->fetch();
        
    }
    
    public function getPidFromListeningToPort($port)
    {
        $listenToPort = $this->listenToPort($port);
        if (!empty($listenToPort)) {
            if ($this->isWindows()) {
                preg_match('/LISTENING[\s]+([0-9]+)/is', $listenToPort, $matches);
            } else {
                preg_match('/LISTEN[\s]+([0-9]+)/is', $listenToPort, $matches);
            }
            return isset($matches[1]) ? $matches[1] : null;
        }
        return null;
    }

    public function makeExecutable($file)
    {
        if ($this->isWindows()) {
            $cmd = null;
        } else {
            $cmd = 'chmod +x %s';
        }

        if (!is_null($cmd)) {
            $output = $this->output;

            $cmd = sprintf($cmd, $file);
            $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
            $process->run(function ($type, $line) use ($output) {
                $output->write($line);
            });
        }
    }

    public function startSeleniumProcess()
    {
        // @todo Refactor this in 5.0; split binaries and drivers; Add Opera.
        // @see https://github.com/bogdananton/Selenium-Setup/issues/12
        $cmdExtra = '';
        if ($binary = $this->config->getBinary('chromedriver.'. $this->getOsName() .'.'. $this->getOsType())) {
            $cmdExtra .= sprintf(' -Dwebdriver.chrome.driver=%s', $this->config->getBuildPath() . DIRECTORY_SEPARATOR . $binary->getBinName());
        }
        if ($binary = $this->config->getBinary('iedriver.'. $this->getOsName() .'.'.$this->getOsType())) {
            $cmdExtra .= sprintf(' -Dwebdriver.ie.driver=%s', $this->config->getBuildPath(). DIRECTORY_SEPARATOR . $binary->getBinName());
        }
        if ($binary = $this->config->getBinary('phantomjs.'. $this->getOsName() .'.'.$this->getOsType())) {
            $cmdExtra .= sprintf(' -Dphantomjs.binary.path=%s', $this->config->getBuildPath(). DIRECTORY_SEPARATOR . $binary->getBinName());
        }
        
        if ($this->isWindows()) {
            $cmd = 'start /b java -jar %s -port %s -Dhttp.proxyHost=%s -Dhttp.proxyPort=%s -log %s %s';
        } else {
            $cmd = 'java -jar %s -port %s -Dhttp.proxyHost=%s -Dhttp.proxyPort=%s -log %s %s >/dev/null 2>&1 &';

        }

        $cmd = vsprintf($cmd, [
            'binary' => $this->config->getBuildPath() . DIRECTORY_SEPARATOR . $this->config->getBinary('selenium')->getBinName(),
            'port' => $this->config->getPort(),
            'proxyHost' => $this->config->getProxyHost(),
            'proxyPort' => $this->config->getProxyPort(),
            'log' => $this->config->getLogsPath() . DIRECTORY_SEPARATOR . 'selenium.log',
            'cmdExtra' => $cmdExtra
        ]);

        //var_dump($cmd);

        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->start();
        // $process->getOutput();
        return $process->getPid();
    }
    
    public function hasXvfb()
    {
        $cmd = 'which Xvfb';
        
        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->start();
        return ($process->getOutput() != '' ? true : false);
    }

    public function startDisplayProcess()
    {
        if ($this->isWindows()) {
            $cmd = null;
        } else {
            if ($this->getEnvVar('DISPLAY')) {
                return true;
            }
            $this->setEnvVar('DISPLAY', ':99.0');
            $cmd = '/sbin/start-stop-daemon --start --pidfile /tmp/custom_xvfb_99.pid --make-pidfile --background --exec /usr/bin/Xvfb -- :99 -ac -screen 0 1280x1024x16';
        }
        $output = new BufferedOutput();
        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
        //var_dump($process->getPid());
        return $process->getPid();
    }
    
    public function getChromeVersion()
    {
        if ($this->isWindows()) {
            $cmd = 'reg query HKEY_LOCAL_MACHINE\SOFTWARE\Wow6432Node\Google\Update\Clients\{8A69D345-D564-463c-AFF1-A69D9E530F96} | findstr /i pv';
            $match = '/REG_SZ[\s]+([0-9.]+)/is';
        } else {
            $cmd = 'google-chrome --version';
            $match = '/Google Chrome ([0-9.]+)/is';
        }
        
        $output = new BufferedOutput();

        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        preg_match($match, $output->fetch(), $matches);

        return isset($matches[1]) ? $matches[1] : null;
    }
    
    public function getFirefoxVersion()
    {
        if ($this->isWindows()) {
            $cmd = 'reg query "HKEY_LOCAL_MACHINE\SOFTWARE\Wow6432Node\Mozilla\Mozilla Firefox" | findstr /i CurrentVersion';
            $match = '/REG_SZ[\s]+([0-9.]+)/is';
        } else {
            $cmd = 'firefox --version';
            $match = '/Mozilla Firefox ([0-9.]+)/is';
        }
        $output = new BufferedOutput();

        $process = new Process($cmd, SeleniumSetup::$APP_ROOT_PATH, SeleniumSetup::$APP_PROCESS_ENV, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        preg_match($match, $output->fetch(), $matches);
        
        return isset($matches[1]) ? $matches[1] : null;
    }
    
}
