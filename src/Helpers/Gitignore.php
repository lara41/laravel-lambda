<?php

namespace Lara41\Utils\Helpers;

class Gitignore
{
    protected $gitignore;

    public function __construct(string $path)
    {
        $this->gitignore = static::parseGitignore($path);
    }

    public function getExcludes()
    {
        return $this;
    }

    public static function parseGitignore(string $path)
    {
        $contents = @file_get_contents(str_finish($path, '/').'.gitignore');

        if ($contents == false) {
            return collect([]);
        }

        return collect(explode("\n", $contents));
    }

    public function toArray()
    {
        return $this->gitignore->toArray();
    }

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->gitignore, $name], $arguments);
    }
}
