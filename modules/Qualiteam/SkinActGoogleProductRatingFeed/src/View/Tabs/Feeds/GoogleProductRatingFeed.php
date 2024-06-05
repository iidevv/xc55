<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\View\Tabs\Feeds;

use Qualiteam\SkinActGoogleProductRatingFeed\Traits\SkinActGoogleProductRatingFeedTrait;
use Qualiteam\SkinActGoogleProductRatingFeed\View\Admin\GoogleProductRatingFeed as GoogleProductRatingFeedView;
use Qualiteam\SkinActGoogleProductRatingFeed\View\Admin\GoogleProductRatingSettings as GoogleProductRatingSettingsView;

class GoogleProductRatingFeed extends \XLite\View\Tabs\ATabs
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
        return [
            $this->getGoogleProductRatingSettingsName() => [
                'weight' => 100,
                'title'  => static::t('SkinActGoogleProductRatingFeed configuration'),
                'widget' => GoogleProductRatingSettingsView::class,
            ],
            $this->getGoogleProductRatingFeedName()     => [
                'weight' => 200,
                'title'  => static::t('SkinActGoogleProductRatingFeed generation'),
                'widget' => GoogleProductRatingFeedView::class,
            ],
        ];
    }

    protected function getDefaultTemplate()
    {
        return 'common/tabs2.twig';
    }
}
