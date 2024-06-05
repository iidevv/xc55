<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Presenter;

use XCart\Container;

class Config
{
    protected function getConfigContainer()
    {
        return Container::getContainer()?->get('yotpo.reviews.configuration');
    }

    public function isWidgetConfigured()
    {
        return $this->getConfigContainer()?->getAppKey()
            && $this->getConfigContainer()?->getSecretKey();
    }

    public function isWidgetStarEnabled()
    {
        return $this->getConfigContainer()?->isShowStarRating();
    }

    public function isWidgetReviewEnabled()
    {
        return $this->getConfigContainer()?->isShowReviewWidget();
    }

    public function getStarWidgetId()
    {
        return $this->getConfigContainer()?->getStarWidgetId();
    }

    public function getReviewWidgetId()
    {
        return $this->getConfigContainer()?->getReviewWidgetId();
    }

    public function isModuleDevMode()
    {
        return $this->getConfigContainer()?->isDevMode();
    }

    public function isWidgetConversionTrackingEnabled()
    {
        return $this->getConfigContainer()?->isConversionTrackingEnable();
    }
}