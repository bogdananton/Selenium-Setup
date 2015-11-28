<?php
namespace SeleniumSetup\System;
use Guzzle\Http\Client;
use Symfony\Component\Process\Exception\ProcessFailedException;
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
        /*
        if ($verbose) {
            echo "\n" . '-----------' . "\n";
            echo $cmd;
            echo "\n" . '-----------' . "\n";
        }
        
        exec($cmd, $output, $return);
        
        //if ($verbose) {
        //    echo implode("\n", $output);
        //    flush();
        //}
        return implode("\n", $output);
        */

        $process = new Process($cmd);
        $process->start();

        $output = null;

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

        // executes after the command finishes
        //if (!$process->isSuccessful()) {
        //    throw new ProcessFailedException($process);
        //}

        //    echo $process->getOutput();

        return $output;
    }

    // @todo put try catch http://stackoverflow.com/questions/16939794/copy-remote-file-using-guzzle
    public function download($from, $to)
    {
        $client = new Client();
        $response = $client->get($from)
            ->setResponseBody($to)
            ->send();
        return true;
    }
}
