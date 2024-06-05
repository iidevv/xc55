<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\View\Form;

use Qualiteam\SkinActGoogleProductRatingFeed\Traits\SkinActGoogleProductRatingFeedTrait;

/**
 * Sitemap Generation form
 */
class FeedGeneration extends \XLite\View\Form\AForm
{
    use SkinActGoogleProductRatingFeedTrait;

    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return $this->getGoogleProductRatingFeedName();
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'generate';
    }
}
