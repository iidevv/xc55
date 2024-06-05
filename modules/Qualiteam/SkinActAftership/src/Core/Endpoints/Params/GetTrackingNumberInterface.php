<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Endpoints\Params;

/**
 * Get tracking number interface
 */
interface GetTrackingNumberInterface
{
    /**
     * Get tracking number
     *
     * @return string
     */
    public function getTrackingNumber(): string;
}