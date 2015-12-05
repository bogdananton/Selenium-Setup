<?php
namespace SeleniumSetup\System;
use GuzzleHttp\Client;
use GuzzleHttp\Event\ProgressEvent;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Class System
 * @package SeleniumSetup\System
 * @todo Replace with http://symfony.com/doc/current/components/filesystem/introduction.html
 */
class System implements SystemInterface
{
    /**
     * @param $dirFullPath
     * @return bool
     */
    public function isDir($dirFullPath)
    {
        return is_dir($dirFullPath);
    }

    /**
     * @param $dirFullPath
     * @return bool
     */
    public function createDir($dirFullPath)
    {
        $makeDir = @mkdir($dirFullPath);
        if (!$makeDir) {
            throw new \RuntimeException(sprintf('Cannot create directory: %s', $dirFullPath));
        } else {
            return true;
        }
    }

    /**
     * @param $fileFullPath
     * @return bool
     */
    public function isFile($fileFullPath)
    {
        return file_exists($fileFullPath);
    }

    /**
     * @param $fileFullPath
     * @param string $contents
     */
    public function createFile($fileFullPath, $contents = '')
    {
        if (!$this->isFile($fileFullPath)) {
            touch($fileFullPath);
        }
        $this->writeToFile($fileFullPath, $contents);
    }

    /**
     * @param $fileFullPath
     * @return bool
     */
    public function isWritable($fileFullPath)
    {
        if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
            $handler = @fopen($fileFullPath, 'a');
            if (!$handler) {
                return false;
            }
            fclose($handler);
            return true;
        } else {
            return @is_writable($fileFullPath);
        }
    }

    /**
     * @param $fileFullPath
     * @param string $contents
     * @return bool
     */
    public function writeToFile($fileFullPath, $contents = '')
    {
        if (!$this->isWritable($fileFullPath)) {
            throw new \RuntimeException(sprintf('File %s is not writable.', $fileFullPath));
        }
        
        $handler = fopen($fileFullPath, 'w');
        
        if (!$handler) {
            throw new \RuntimeException(sprintf('Cannot open %s file.', $fileFullPath));
        }
        
        $write = fwrite($handler, $contents);
        
        if ($write === false) {
            throw new \RuntimeException(sprintf('Cannot write to %s file.', $fileFullPath));
        }
        
        fclose($handler);
        
        return true;
    }

    /**
     * @param $fileFullPath
     * @return string
     */
    public function readFile($fileFullPath)
    {
        $handler = fopen($fileFullPath, 'r');

        if (!$handler) {
            throw new \RuntimeException(sprintf('Cannot open %s file.', $fileFullPath));
        }
        
        $contents = fread($handler, filesize($fileFullPath));
        
        return $contents;
    }

    /**
     * @param $fileFullPath
     * @return resource
     */
    public function openFileForReading($fileFullPath)
    {
        return fopen($fileFullPath, 'r');
    }

    /**
     * @param $handler
     * @param int $limit
     * @param string $separator
     * @return array
     */
    public function readFileLineAsCsv($handler, $limit = 0, $separator = '|')
    {
        return fgetcsv($handler, $limit, $separator);
    }

    /**
     * @param $cmd
     * @param bool|false $verbose
     * @return string
     */
    public function execCommand($cmd, $verbose = false)
    {
        // var_dump($cmd);

        $output = null;

        try {
            $process = new Process($cmd);
            $process->start();
            $process->setIdleTimeout(0);
            $process->setTimeout(0);

            $process->wait(function ($type, $buffer) use (&$output, $verbose) {
                //if (Process::ERR === $type) {
                //    echo 'ERR > '.$buffer;
                //} else {
                //    echo 'OUT > '.$buffer;
                //}
                $output .= $buffer;
                if ($verbose) {
                    echo $buffer;
                }
            });

        } catch (RuntimeException $e) {
            var_dump ($e->getMessage());
        }

        // executes after the command finishes
        //if (!$process->isSuccessful()) {
        //    throw new ProcessFailedException($process);
        //}

        //    echo $process->getOutput();
        //var_dump($cmd);
        //var_dump($process->getExitCode());
        //var_dump($process->getExitCodeText());
        return $output;
    }

    // @todo put try catch http://stackoverflow.com/questions/16939794/copy-remote-file-using-guzzle
    public function download($from, $to)
    {

        $client = new Client();
        $client->setDefaultOption('verify', false); //, dirname(__FILE__) . '/../../bin/cacert.pem');
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

        $request->getEmitter()->on('progress', function (ProgressEvent $e) use ($computeRemainingSize) {
            echo sprintf(
                "Downloaded %s%%\r", $computeRemainingSize($e)
            );
        });

        $client->send($request);

        return true;
    }

    public function rename($from, $to)
    {
        return rename($from, $to);
    }


}
