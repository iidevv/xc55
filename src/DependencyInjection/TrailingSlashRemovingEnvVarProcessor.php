<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\DependencyInjection;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

class TrailingSlashRemovingEnvVarProcessor implements EnvVarProcessorInterface
{
    public function getEnv(string $prefix, string $name, \Closure $getEnv)
    {
        $env = $getEnv($name);

        return rtrim($env, '/');
    }

    public static function getProvidedTypes()
    {
        return [
            'remove_trailing_slash' => 'string',
        ];
    }
}
