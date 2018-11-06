<?php

namespace Lara41\Utils\Helpers;

use ZipArchive;

class Zip
{
    protected $path;
    protected $zip;

    public function __construct(string $path)
    {
        if (! extension_loaded('zip')) {
            throw new \Exception('ZIP Extension not loaded.');
        }

        $this->zip = app(ZipArchive::class);

        $this->zip->open($this->path = $path);
    }

    public static function fromDir(string $path, array $excludes, string $destination)
    {
        return app(ZipCreator::class)->create($path, $excludes, $destination);
    }


    public function addFileFromString(string $contents, string $filename) : self
    {
        if (!$this->zip->addFromString($filename, $contents)) {
            throw new \RuntimeException("There was an error when adding the file.");
        }

        return $this;
    }

    public function addFile(string $file, string $filename) : self
    {
        if (!$this->zip->addFile($file, $filename)) {
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
