<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\View\Customer\ProductInfo\Details;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Container;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AverageRating extends \XC\Reviews\View\Customer\ProductInfo\Details\AverageRating
{
    protected function getDefaultTemplate()
    {
        if ($this->isYotpoEnabled()) {
            return Module::getModulePath() . 'modules/XC/Reviews/reviews_page/rating/body.twig';
        }

        return parent::getDefaultTemplate();
    }

    /**
     * Always off. If you want to show yotpo stars, added a condition
     */
    protected function isYotpoEnabled()
    {
        return false;
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