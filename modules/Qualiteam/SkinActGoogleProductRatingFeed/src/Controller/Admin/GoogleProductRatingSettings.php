<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\Controller\Admin;

use Qualiteam\SkinActGoogleProductRatingFeed\Core\Task\FeedUpdater;
use XLite\Core\Database;
use XLite\Model\Config;
use XLite\Core\Config as CoreConfig;
use Qualiteam\SkinActGoogleProductRatingFeed\View\Model\Settings\GoogleProductRatingSettings as FeedSettings;

class GoogleProductRatingSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('SkinActGoogleProductRatingFeed feeds');
    }

    /**
     * Get options category
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'Qualiteam\SkinActGoogleProductRatingFeed';
    }

    protected function getModelFormClass()
    {
        return FeedSettings::class;
    }

    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');

        FeedUpdater::setRenewalPeriod(
            CoreConfig::getInstance()->Qualiteam->SkinActGoogleProductRatingFeed->google_rating_renewal_frequency
        );
    }

    /**
     * Returns shipping options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->executeCachedRuntime(function () {
            return Database::getRepo(Config::class)
                ->findByCategoryAndVisible($this->getOptionsCategory());
        }, [__CLASS__, __METHOD__]);
    }
}
