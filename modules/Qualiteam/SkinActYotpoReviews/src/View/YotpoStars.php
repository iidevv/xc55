<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Container;
use XLite\View\AView;
use XLite\View\CacheableTrait;

class YotpoStars extends AView
{
    use CacheableTrait;

    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();
        $list[] = $this->getProductSku();

        return $list;
    }

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->setPresenterProduct();
    }

    /**
     * @return void
     */
    protected function setPresenterProduct()
    {
        $this->getPresenterProduct()?->setProduct(
            $this->getProduct()
        );
    }

    /**
     * @return \Qualiteam\SkinActYotpoReviews\Presenter\YotpoReviews|null
     */
    protected function getPresenterProduct()
    {
        return Container::getContainer()
            ?->get('yotpo.reviews.presenter.reviews');
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
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return Module::getModulePath() . 'stars/body.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = Module::getModulePath() . 'stars/style.less';

        return $list;
    }

    /**
     * @return string
     */
    protected function getProductSku()
    {
        return $this->getPresenterProduct()?->getProductSku();
    }

    protected function getWidgetId()
    {
        return $this->getPresenterConfig()?->getStarWidgetId();
    }

    /**
     * Always off. If you want to show yotpo stars, added a condition and parent::isVisible
     */
    protected function isVisible()
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
}