<?php

namespace Lara41\Utils\Helpers;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Symfony\Component\Finder\Finder;

class ZipCreator
{
    const EXCLUDES = ['.', '..', '.env', '.git'];

    public function __construct()
    {
        $this->zip = app(ZipArchive::class);
        $this->finder = app(Finder::class);
    }

    public function create(string $path, array $excludes = [], string $tempdest)
    {
        if (!$this->zip->open($tempdest, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            throw new \RuntimeException("Can't create zip");
        }

        $this->addToZip(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path),
                RecursiveIteratorIterator::LEAVES_ONLY
            ),
            $path,
            $this->getExcludes($excludes)
        );

        $this->zip->close();

        return new Zip($tempdest);
    }

    protected function getExcludes(array $excludes = [])
    {
        return array_unique(array_merge(static::EXCLUDES, $excludes), SORT_STRING);
    }

    protected function addToZip($files, string $rootPath, array $excludes)
    {
        foreach ($files as $name => $file) {
            if ($file->isDir()) {
                continue;
            }

            $filePath = substr($file->getRealPath(), strlen($rootPath) + 1);

            $filePath = str_replace('\\', '/', $filePath);

            $parts = explode('/', $filePath);

            if (in_array($parts[count($parts) - 1], $excludes)) {
                continue;
            }

            $this->zip->addFile($filePath, $filePath);

            if (in_array('php-cgi', $parts)) {
                $this->zip->setExternalAttributesName($filePath, ZipArchive::OPSYS_UNIX, 2180972544);
            }
        }
    }
}
