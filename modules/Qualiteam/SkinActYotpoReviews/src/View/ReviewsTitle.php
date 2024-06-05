<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Container;
use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;
use XLite\View\AView;
use XLite\View\CacheableTrait;

/**
 * @ListChild (list="product.reviews.tab.header", weight="10")
 *
 * @Extender\Depend("XC\Reviews")
 */
class ReviewsTitle extends AView
{
    use CacheableTrait;

    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();
        $list[] = $this->getProduct()->getProductId();

        return $list;
    }

    protected function getDefaultTemplate()
    {
        return Module::getModulePath() . 'modules/XC/Reviews/reviews_tab/parts/reviews-header.title.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && !$this->isYotpoEnabled();
    }

    /**
     * @return bool
     */
    protected function isYotpoEnabled()
    {
        return $this->isYotpoConfigured()
            && $this->getPresenterConfig()?->isWidgetReviewEnabled();
    }

    protected function isYotpoConfigured()
    {
        return $this->getPresenterConfig()?->isWidgetConfigured();
    }

    /**
     * @return object|\Qualiteam\SkinActYotpoReviews\Presenter\Config|null
     */
    protected function getPresenterConfig()
    {
        return Container::getContainer()
            ?->get('yotpo.reviews.presenter.config');
    }
}