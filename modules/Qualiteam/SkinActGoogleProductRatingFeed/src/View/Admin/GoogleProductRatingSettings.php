<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\View\Admin;

use Qualiteam\SkinActGoogleProductRatingFeed\Traits\SkinActGoogleProductRatingFeedTrait;

class GoogleProductRatingSettings extends \XLite\View\AView
{
    use SkinActGoogleProductRatingFeedTrait;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = (new GoogleProductRatingSettings)->getGoogleProductRatingSettingsName();

        return $result;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/admin/settings.twig';
    }
}
