<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Container;
use Qualiteam\SkinActYotpoReviews\Presenter\JSYotpoConversionTracking as JSYotpoConversionTrackingPresenter;
use XCart\Extender\Mapping\ListChild;
use XLite\View\AView;

/**
 * @ListChild (list="center")
 */
class JSYotpoConversionTracking extends AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'checkoutSuccess';

        return $list;
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isWidgetConfigured()
            && $this->isWidgetEnabled()
            && $this->isModuleDevMode();
    }

    protected function isWidgetConfigured()
    {
        return $this->getPresenterConfig()?->isWidgetConfigured();
    }

    protected function isWidgetEnabled()
    {
        return $this->getPresenterConfig()?->isWidgetConversionTrackingEnabled();
    }

    protected function isModuleDevMode()
    {
        return $this->getPresenterConfig()?->isModuleDevMode();
    }

    /**
     * @return object|\Qualiteam\SkinActYotpoReviews\Presenter\Config|null
     */
    protected function getPresenterConfig()
    {
        return Container::getContainer()
            ?->get('yotpo.reviews.presenter.config');
    }

    /**
     * @return string
     */
    public function getHTML(): string
    {
        return $this->getYotpoConversionTrackingScript();
    }

    /**
     * @return string
     */
    protected function getYotpoConversionTrackingScript(): string
    {
        return $this->getPresenterConversion()?->getYotpoConversionTrackingScript($this->getOrder());
    }

    /**
     * @return \Qualiteam\SkinActYotpoReviews\Presenter\JSYotpoConversionTracking|null
     */
    protected function getPresenterConversion(): ?JSYotpoConversionTrackingPresenter
    {
        return Container::getContainer()?->get('yotpo.reviews.presenter.js.conversion.tracking');
    }

    protected function getDefaultTemplate()
    {
        return Module::getModulePath() . 'conversion_tracking/body.twig';
    }
}