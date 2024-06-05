<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Helpers;

class Controller
{
    protected $zones;

    public function __construct()
    {
        $this->zones = [
            'Customer',
            'Admin',
        ];
    }

    /**
     * @param $target
     *
     * @return array
     */
    public function isControllerExistsInZones($target)
    {
        $zones = array_fill_keys(
            $this->zones,
            false
        );

        foreach ($zones as $key => $zone) {
            $zones[$key] = \XLite\Core\Converter::getControllerClassInZone($target, $zone);
        }

        return $zones;
    }
}
