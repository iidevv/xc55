<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\FormField\Select;

/**
 * Parcel puck up type selector
 */
class PickUpType extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            \XC\CanadaPost\Core\API::PICKUP_TYPE_AUTO   => static::t('shipments are picked up by Canada Post'),
            \XC\CanadaPost\Core\API::PICKUP_TYPE_MANUAL => static::t('deposit your items at a Post Office'),
        ];
    }
}
