<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View;

use Qualiteam\SkinActYotpoReviews\Module;
use Qualiteam\SkinActYotpoReviews\Presenter\JSYotpoReviews as JSYotpoReviewsPresenter;
use XCart\Container;
use XCart\Extender\Mapping\ListChild;
use XLite\View\AView;

/**
 * @ListChild (list="jscontainer.js", zone="customer", weight="999999")
 */
class JSYotpoReviews extends AView
{
    public static function getAllowedTargets()
    {
        return ['product'];
    }

    /**
     * @return string
     */
    public function getHTML(): string
    {
        return $this->getYotpoReviewsScript();
    }

    /**
     * @return string
     */
    protected function getYotpoReviewsScript(): string
    {
        return $this->getPresenter()?->getYotpoStarsScript();
    }

    /**
     * @return \Qualiteam\SkinActYotpoReviews\Presenter\JSYotpoReviews|null
     */
    protected function getPresenter(): ?JSYotpoReviewsPresenter
    {
        return Container::getContainer()?->get('yotpo.reviews.presenter.js');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return Module::getModulePath() . 'js_yotpo.twig';
    }
}