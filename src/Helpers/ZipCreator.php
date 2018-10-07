<?php

namespace Lara41\Utils\Helpers;

use ZipArchive;
use Facades\Symfony\Component\Finder\Finder;

class ZipCreator
{
    public function __construct()
    {
        $this->zip = app(ZipArchive::class);
    }

    public function create(string $path, array $excludes = [], string $tempdest)
    {
        if (! $this->zip->open($tempdest, ZipArchive::CREATE)) {
            throw new \RuntimeException("Can't create zip");
        }

        $this->addToZip(Finder::in($path)->exclude($excludes));

        $zip->close();

        return tap(new Zip(file_get_contents($tempdest)), function () use ($tempdest) {
            unlink($tempdest);
        });
    }

    protected function addToZip($iterator)
    {
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $this->addToZip(Finder::in($item->getRealPath()));
            }

            $this->zip->addFile($item->getFilename());
        }
    }
}
