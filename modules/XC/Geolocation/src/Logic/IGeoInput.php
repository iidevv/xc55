<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\Logic;

/**
 * Geo input interface
 */
interface IGeoInput
{
    /**
     * Returns scalar representation of internal geo data.
     *
     * @return mixed
     */
    public function getData();

    /**
     * Returns hash of geo data, is used as key in cache.
     *
     * @return string
     */
    public function getHash();
}
