<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Firebase\JWT\JWT;

class AuthToken
{
    /**
     * @return string
     */
    protected static function getDefaultAlgorithm(): string
    {
        return 'HS256';
    }

    /**
     * @return string
     */
    public static function getDefaultExpirationTime(): string
    {
        return Converter::time() + Session::MAX_ADMIN_TTL;
    }

    /**
     * @return string
     */
    protected static function getDefaultKey(): string
    {
        return \Includes\Utils\ConfigParser::getOptions(['installer_details', 'auth_code']) ?? '';
    }

    /**
     * @param mixed $payload
     * @param string|null $key
     * @param string|null $alg
     *
     * @return string
     */
    public static function generate($payload = null, $key = null, $alg = null): string
    {
        return JWT::encode(
            $payload,
            $key ?? static::getDefaultKey(),
            $alg ?? static::getDefaultAlgorithm()
        );
    }

    /**
     * @param string $token
     * @param string|null $key
     * @param string|null $alg
     *
     * @return object
     * @throws \Exception
     */
    public static function decode($token, $key = null, $alg = null): object
    {
        try {
            return JWT::decode(
                $token,
                $key ?? static::getDefaultKey(),
                [
                    $alg ?? static::getDefaultAlgorithm()
                ]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
