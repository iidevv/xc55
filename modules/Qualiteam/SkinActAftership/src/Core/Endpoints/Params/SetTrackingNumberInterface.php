<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Endpoints\Params;

/**
 * Set tracking number interface
 */
interface SetTrackingNumberInterface
{
    const PARAM_TRACKING_NUMBER = 'tracking_number';

    /**
     * Set tracking number
     *
     * @param string|int $trackingNumber
     *
     * @return void
     */
    public function setTrackingNumber(string|int $trackingNumber): void;
}