<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View;

use Qualiteam\SkinActYotpoReviews\Module;
use XC\Reviews\View\Customer\ProductInfo\ItemsList\AverageRating;
use XCart\Container;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After("XC\CrispWhiteSkin")
 */
class ItemsListAverageRating extends AverageRating
{
    protected function getDefaultTemplate()
    {
        if ($this->isShowYotpoStars()) {
            return Module::getModulePath() . 'modules/XC/Reviews/product.items_list.rating.twig';
        }

        return parent::getDefaultTemplate();
    }

    /**
     * Always off. If you want to show yotpo stars, added a condition
     */
    protected function isShowYotpoStars()
    {
        return false;
    }

    protected function isWidgetConfigured()
    {
        return $this->getPresenterConfig()?->isWidgetConfigured();
    }

    protected function isWidgetEnabled()
    {
        return $this->getPresenterConfig()?->isWidgetStarEnabled();
    }

    /**
     * @return object|\Qualiteam\SkinActYotpoReviews\Presenter\Config|null
     */
    protected function getPresenterConfig()
    {
        return Container::getContainer()
            ?->get('yotpo.reviews.presenter.config');
    }

    public function isVisibleAddReviewLink($product = null)
    {
        return parent::isVisibleAddReviewLink($product)
            && $this->offVisibleAddReviewLink();
    }

    protected function offVisibleAddReviewLink()
    {
        return false;
    }
}