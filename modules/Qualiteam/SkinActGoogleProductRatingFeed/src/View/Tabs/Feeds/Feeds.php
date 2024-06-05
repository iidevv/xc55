<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\View\Tabs\Feeds;

use Qualiteam\SkinActGoogleProductRatingFeed\Traits\SkinActGoogleProductRatingFeedTrait;
use XCart\Extender\Mapping\Extender;
use Qualiteam\SkinActGoogleProductRatingFeed\View\Tabs\Feeds\GoogleProductRatingFeed;

/**
 * @Extender\Mixin
 */
class Feeds extends \XLite\View\Tabs\Feeds
{
    use SkinActGoogleProductRatingFeedTrait;

    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'google_product_rating_settings';
        $list[] = 'google_product_rating_feed';

        return $list;
    }

    protected function defineTabs()
    {
        $list = parent::defineTabs();

        $list[$this->getGoogleProductRatingFeedName()] = [
            'weight'     => 500,
            'title'      => static::t('SkinActGoogleProductRatingFeed google product rating feed'),
            'references' => [
                ['target' => $this->getGoogleProductRatingSettingsName()],
            ],
            'widget'     => GoogleProductRatingFeed::class,
        ];

        return $list;
    }
}
