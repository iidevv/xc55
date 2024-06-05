<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Utils;

/**
 * Get slug by name
 */
class Slug
{
    /**
     * Get slug by name.
     * Example: "Fedex [fedex]" => "fedex"
     *
     * @param string $name
     *
     * @return string
     */
    public static function getSlugByName(string $name): string
    {
        preg_match('/\[([^\]]+?)\]/m', $name, $matches);

        return $matches ? $matches[1] : '';
    }
}