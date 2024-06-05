<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Validators;

class ResponseValidator
{
    /**
     * @param array $response
     *
     * @return bool
     */
    public static function isValid(array $response): bool
    {
        return self::isSuccess($response);
    }

    /**
     * @param array $response
     *
     * @return bool
     */
    protected static function isSuccess(array $response): bool
    {
        return !isset($response['success']);
    }
}