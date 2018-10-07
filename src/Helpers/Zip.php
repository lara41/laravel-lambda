<?php

namespace Lara41\Utils\Helpers;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Zip
{
    protected $path;
    protected $zip;

    public function __construct(string $zipContent)
    {
        file_put_contents($this->path = storage_path('tmp/'.$fileName = str_random(10).'.zip'), $zipContent);

        $this->zip = app(ZipArchive::class);

        $this->zip->open($this->path);
    }

    public static function fromDir($path, $destination)
    {
        $zip = app(ZipArchive::class);

        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $path = str_replace('\\', '/', realpath($path));

        if (is_dir($path) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/')+1), array('.', '..'))) {
                    continue;
                }

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($path . '/', '', $file . '/'));
                } elseif (is_file($file) === true) {
                    $zip->addFromString(str_replace($path . '/', '', $file), file_get_contents($file));
                }
            }
        } elseif (is_file($path) === true) {
            $zip->addFromString(basename($path), file_get_contents($path));
        }

        $zip->close();

        return new self(file_get_contents($destination));
    }


    public function addFileFromString(string $contents, string $filename) : self
    {
        if (! $this->zip->addFromString($filename, $contents)) {
            throw new \RuntimeException("There was an error when adding the file.");
        }

        return $this;
    }

    public function addFile(string $file, string $filename) : self
    {
        if (! $this->zip->addFile($file, $filename)) {
            throw new \RuntimeException("There was an error when adding the file.");
        }

        return $this;
    }

    public function getContents() : string
    {
        if (! $this->zip->close()) {
            throw new \RuntimeException("There was an error when saving the zip.");
        }
        return file_get_contents($this->path);
    }

    public function saveTo(string $path)
    {
        if ($this->path == $path) {
            $this->getContents();
        }

        file_put_contents($path, $this->getContents());
    }

    public function getClient()
    {
        return $this->zip;
    }

    public function __toString() : string
    {
        return $this->getContents();
    }

    public function __destruct()
    {
        unlink($this->path);
    }
}
