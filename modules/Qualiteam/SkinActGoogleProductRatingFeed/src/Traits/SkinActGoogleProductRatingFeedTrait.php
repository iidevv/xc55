<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\Traits;

trait SkinActGoogleProductRatingFeedTrait
{
    /**
     * Get google product rating feed name
     *
     * @return string
     */
    protected function getGoogleProductRatingFeedName(): string
    {
        return 'google_product_rating_feed';
    }

    /**
     * Get google product rating settings name
     *
     * @return string
     */
    protected function getGoogleProductRatingSettingsName(): string
    {
        return 'google_product_rating_settings';
    }

    /**
     * Get current module path
     *
     * @return string
     */
    protected function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActGoogleProductRatingFeed';
    }
}