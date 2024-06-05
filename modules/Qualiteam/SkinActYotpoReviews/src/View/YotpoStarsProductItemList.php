<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Container;
use XLite\Model\WidgetParam\TypeObject;
use XLite\View\AView;
use XLite\View\CacheableTrait;

class YotpoStarsProductItemList extends AView
{
    use CacheableTrait;

    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();
        $list[] = $this->getProductSku();

        return $list;
    }

    public const PARAM_PRODUCT     = 'product';

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->setPresenterProduct();
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = Module::getModulePath() . 'items_list/stars/style.less';

        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return Module::getModulePath() . 'items_list/stars/body.twig';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRODUCT => new TypeObject('Product', null, false, 'XLite\Model\Product'),
        ];
    }

    protected function prepareProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * @return void
     */
    protected function setPresenterProduct()
    {
        $product = $this->prepareProduct() ?? $this->getProduct();

        $this->getPresenterProduct()?->setProduct(
            $product
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

    protected function getPresenterConfig()
    {
        return Container::getContainer()
            ?->get('yotpo.reviews.presenter.config');
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

    protected function getProductUrl()
    {
        return $this->getPresenterProduct()?->getProductUrl();
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

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActYotpoReviews/items_list/stars/YotpoStarsProductItemList.js';
        return $list;
    }
}