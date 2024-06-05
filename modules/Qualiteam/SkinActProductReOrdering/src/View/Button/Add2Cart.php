<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\View\Button;

class Add2Cart extends \XLite\View\Button\AButton
{
    const PARAM_PRODUCT = 'product';

    protected function getDefaultButtonClass()
    {
        return parent::getDefaultButtonClass() . ' reorder-add2cart regular-main-button icon-cart-add';
    }

    protected function getDefaultLabel()
    {
        return static::t('Add to cart');
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActProductReOrdering/add2cart/controller.js';

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActProductReOrdering/add2cart/style.less';

        return $list;
    }

    protected function getTemplate()
    {
        return 'modules/Qualiteam/SkinActProductReOrdering/items_list/re_order/button/add2cart.twig';
    }

    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRODUCT      => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
        ];
    }
    public function getAvailableAmount()
    {
        return $this->getProduct() ? $this->getProduct()->getAvailableAmount() : 0;
    }

    public function isAllStockInCart()
    {
        return $this->getProduct() ? $this->getProduct()->isAllStockInCart() : false;
    }

    public function getItemsInCartMessage()
    {
        return $this->getProduct() ? $this->getProduct()->getItemsInCartMessage() : '';
    }
}