<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\View\ItemsList\Model;

class ReOrder extends \XLite\View\ItemsList\AItemsList
{
    const PARAM_PRODUCT = 'product';

    const WIDGET_TARGET = 're_order';

    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = static::getWidgetTarget();

        return $result;
    }

    protected function isPagerVisible()
    {
        return 0 < $this->getItemsCount();
    }

    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActProductReOrdering/items_list';
    }

    protected function getPageBodyDir()
    {
        return 're_order';
    }

    protected function getPagerClass()
    {
        return 'Qualiteam\SkinActProductReOrdering\View\Pager\Customer\ReOrder';
    }

    protected function defineRepositoryName()
    {
        return 'XLite\Model\Product';
    }

    protected function getListName()
    {
        return parent::getListName() . '.reorder-product';
    }

    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' items-list-reorder';
    }

    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->{\Qualiteam\SkinActProductReOrdering\Model\Repo\Product::RE_ORDER_PROFILE_ID} =
            \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();

        return parent::getData($cnd, $countOnly);
    }

    protected function isHeaderVisible()
    {
        return false;
    }

    protected static function getWidgetTarget()
    {
        return static::WIDGET_TARGET;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActProductReOrdering/items_list/controller.js';

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActProductReOrdering/items_list/style.less';

        return $list;
    }

    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
        ];
    }

    protected function getQuantity()
    {
        return 1;
    }

    public function getProductId()
    {
        return $this->getProduct() ? $this->getProduct()->getProductId() : 0;
    }

    public function getCurrency()
    {
        return \XLite::getInstance()->getCurrency();
    }

    public function getLastOrderItemAttributes()
    {
        return $this->getProduct() && $this->getProduct()->getLastOrderItem()
            ? $this->getProduct()->getLastOrderItem()->getAttributeValues()
            : [];
    }

    public function getDisplayProductPrice()
    {
        return $this->getProduct() && $this->getProduct()->getLastOrderItem()
            ? $this->getProduct()->getLastOrderItem()->getDisplayPrice()
            : $this->getProduct()->getDisplayPrice();
    }

    public function hasImage()
    {
        return $this->getProduct() ? $this->getProduct()->hasImage() : '';
    }

    public function getProductURL()
    {
        return $this->getProduct() ? $this->getProduct()->getURL() : '';
    }

    public function getImage()
    {
        return $this->getProduct() ? $this->getProduct()->getImage() : '';
    }

    public function getName()
    {
        return $this->getProduct() ? $this->getProduct()->getName() : '';
    }

    public function getWeight()
    {
        return $this->getProduct() ? $this->getProduct()->getWeight() : 0;
    }
}