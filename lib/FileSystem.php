<?php
namespace SeleniumSetup;

/**
 * Class System
 * @package SeleniumSetup\System
 * @todo Replace with http://symfony.com/doc/current/components/filesystem/introduction.html
 */
class FileSystem implements FileSystemInterface
{
    /**
     * @var string|false
     */
    protected $certificatePath = false;

    /**
     * @param $dirFullPath
     * @return bool
     */
    public function isDir($dirFullPath)
    {
        return is_dir($dirFullPath);
    }

    public function isPathAbsolute($path)
    {
        preg_match('$/[a-zA-Z]\:/', $path, $matches); /** @todo check regex on win */
        return (substr($path, 0, 1) === '/') || (count($matches) > 0);
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

    public function rename($from, $to)
    {
        return rename($from, $to);
    }
}
