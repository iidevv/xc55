<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Endpoints\Trackings;

use Qualiteam\SkinActAftership\Core\Endpoints\Params\SetSlugInterface;
use Qualiteam\SkinActAftership\Core\Endpoints\Params\SetTrackingNumberInterface;

/**
 * Class create trackings
 */
class PostTrackings extends \Qualiteam\SkinActAftership\Core\Endpoints\ATrackings implements SetSlugInterface, SetTrackingNumberInterface
{
    /**
     * Set tracking number
     *
     * @param string|int $trackingNumber
     *
     * @return void
     */
    public function setTrackingNumber(string|int $trackingNumber): void
    {
        $this->addTrackingParam(static::PARAM_TRACKING_NUMBER, $trackingNumber);
    }

    /**
     * Set slug param
     *
     * @param string|array $slug
     *
     * @return void
     */
    public function setSlug(string|array $slug): void
    {
        $this->addTrackingParam(static::PARAM_SLUG, $slug);
    }
}