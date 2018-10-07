<?php

namespace Lara41\Utils\Helpers;

use Facades\Symfony\Component\OptionsResolver\OptionsResolver;

class HandlerGenerator
{
    const REGEX = '/(let app = JSON.parse\(\').*(\'\) ?\/\/.* ?lar41-properties)/m';

    const STORAGE_PATH = 'laravel-handler/handler.js';

    public static function generate(array $options) : string
    {
        $options = OptionsResolver::setRequired(['host', 'prefix', 'https'])->resolve($options);

        return preg_replace(self::REGEX, '$1'.json_encode($options).'$2', file_get_contents(__DIR__.'../../utils/'.self::STORAGE_PATH));
    }
}
