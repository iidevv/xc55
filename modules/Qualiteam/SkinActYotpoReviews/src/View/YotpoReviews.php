<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Container;
use XCart\Extender\Mapping\ListChild;
use XLite\View\AView;
use XLite\View\CacheableTrait;

/**
 * @ListChild (list="product.reviews.page", weight="200")
 * @ListChild (list="product.reviews.tab", weight="200")
 */
class YotpoReviews extends AView
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
     * @return object|\Qualiteam\SkinActYotpoReviews\Presenter\YotpoReviews|null
     */
    protected function getPresenterProduct()
    {
        return Container::getContainer()
            ?->get('yotpo.reviews.presenter.reviews');
    }

    protected function getPresenterConfig()
    {
        return Container::getContainer()
            ?->get('yotpo.reviews.presenter.config');
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = Module::getModulePath() . 'reviews/style.less';

        return $list;
    }

    protected function getWidgetId()
    {
        return $this->getPresenterConfig()?->getReviewWidgetId();
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return Module::getModulePath() . 'reviews/body.twig';
    }

    /**
     * @return string
     */
    protected function getProductName()
    {
        return $this->getPresenterProduct()?->getProductName();
    }

    /**
     * @return string
     */
    protected function getProductSku()
    {
        return $this->getPresenterProduct()?->getProductSku();
    }

    /**
     * @return string
     */
    protected function getProductUrl()
    {
        return $this->getPresenterProduct()?->getProductUrl();
    }

    /**
     * @return string
     */
    protected function getProductImageUrl()
    {
        return $this->getPresenterProduct()?->getProductImageUrl();
    }

    /**
     * @return float
     */
    protected function getProductPrice()
    {
        return $this->getPresenterProduct()?->getProductPrice();
    }

    /**
     * @return string
     */
    protected function getCurrency()
    {
        return $this->getPresenterProduct()?->getCurrency();
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isWidgetConfigured()
            && $this->isWidgetEnabled();
    }

    protected function isWidgetConfigured()
    {
        return $this->getPresenterConfig()?->isWidgetConfigured();
    }

    protected function isWidgetEnabled()
    {
        return $this->getPresenterConfig()?->isWidgetReviewEnabled();
    }
}