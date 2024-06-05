<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Taxes;

/**
 * Zone selector
 */
class Zone extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get zones list
     *
     * @return array
     */
    protected function getZonesList()
    {
        $list = [];
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Zone')->findAllZones() as $e) {
            $list[$e->getZoneId()] = $e->getZoneName();
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->getZonesList();
    }
}
