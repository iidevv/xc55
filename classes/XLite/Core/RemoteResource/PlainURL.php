<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\RemoteResource;

class PlainURL extends AURL
{
    public static function isMatch($url)
    {
        return static::isURL($url);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function convertURL($url)
    {
        return $url;
    }
}
