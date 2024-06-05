<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\View\Admin;

use Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed\Generator;
use Qualiteam\SkinActGoogleProductRatingFeed\Main;
use Qualiteam\SkinActGoogleProductRatingFeed\Traits\SkinActGoogleProductRatingFeedTrait;
use Qualiteam\SkinActGoogleProductRatingFeed\Core\Task\FeedUpdater;

class GoogleProductRatingFeed extends \XLite\View\Dialog
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
        $result[] = (new GoogleProductRatingFeed)->getGoogleProductRatingFeedName();

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return $this->getModulePath() . '/admin';
    }

    /**
     * @return bool
     */
    protected function isFeedGenerated(): bool
    {
        return Generator::getInstance() && Generator::getInstance()->isGenerated();
    }

    /**
     * Get google feed URL
     *
     * @return string
     */
    protected function getFeedURL(): string
    {
        return Main::getGoogleProductRatingFeedUrl();
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function getRenewalFrequency(): bool
    {
        return FeedUpdater::getRenewalPeriod();
    }
}
