<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Controller\Customer;

use Qualiteam\SkinActAftership\Helpers\TrackingsHelper;

/**
 * Class trackings
 */
class Trackings extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle(): string
    {
        return TrackingsHelper::getTitle();
    }

    /**
     * Get info for carriers
     *
     * @return array
     */
    public function postCouriersDetect(): array
    {
        return TrackingsHelper::postCouriersDetect();
    }

    /**
     * Get tracking
     *
     * @return array
     */
    public function getTrackings(): array
    {
        return TrackingsHelper::getTrackings();
    }

    /**
     * Get tracking number
     *
     * @return string|null
     */
    public function getTrackingNumber(): ?string
    {
        return TrackingsHelper::getTrackingNumber();
    }

    /**
     * Get slug
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return TrackingsHelper::getSlug();
    }

    /**
     * @return array
     */
    public function getError(): array
    {
        return TrackingsHelper::getError();
    }
}