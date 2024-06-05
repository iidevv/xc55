<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\View\Product;

use Qualiteam\SkinActProMembership\Helpers\ProMembershipProducts;
use XLite\Model\Product;

class QuantityBox extends \XLite\View\Product\AProduct
{
    public const PARAM_FIELD_NAME   = 'fieldName';
    public const PARAM_FIELD_TITLE  = 'fieldTitle';
    public const PARAM_PRODUCT      = 'product';
    public const PARAM_FIELD_VALUE  = 'fieldValue';
    public const PARAM_MAX_VALUE    = 'maxValue';
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/quantity_box.less';

        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActProductReOrdering/quantity_box';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_FIELD_NAME   => new \XLite\Model\WidgetParam\TypeString('Name', 'amount'),
            static::PARAM_FIELD_TITLE  => new \XLite\Model\WidgetParam\TypeString('Title', 'Quantity'),
            static::PARAM_PRODUCT      => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
            static::PARAM_FIELD_VALUE  => new \XLite\Model\WidgetParam\TypeInt('Value', null),
            static::PARAM_MAX_VALUE    => new \XLite\Model\WidgetParam\TypeInt('Max value', null),
        ];
    }

    protected function getBoxName()
    {
        return $this->getParam(static::PARAM_FIELD_NAME);
    }

    /**
     * @return Product|null|mixed
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    protected function getBoxId()
    {
        return $this->getBoxName() . $this->getProduct()->getProductId();
    }

    protected function getBoxValue()
    {
        $value = $this->getParam(static::PARAM_FIELD_VALUE) ?: $this->getProduct()->getMinPurchaseLimit();

        return max($value, $this->getMinQuantity());
    }

    protected function getBoxTitle()
    {
        return $this->getParam(static::PARAM_FIELD_TITLE);
    }

    protected function getClass()
    {
        return trim(
            'quantity'
            . ' validate[required,custom[integer],min[' . $this->getMinQuantity() . ']'
            . $this->getAdditionalValidate()
            . ']',
        );
    }

    protected function getAdditionalValidate()
    {
        return $this->getProduct()->getInventoryEnabled() ? ',max[' . $this->getMaxQuantity() . ']' : '';
    }

    protected function getMaxQuantity()
    {
        $product = $this->getProduct();

        return $product && $product->hasVariants()
            ? $this->getAvailableAmount()
            : $this->getDefaultMaxQuantity();
    }

    protected function getDefaultMaxQuantity()
    {
        $maxValue = $this->getParam(static::PARAM_MAX_VALUE);
        if ($this->isPaidMembershipProduct()) {
            $maxValue = 1;
        }

        return $maxValue ?? $this->getProduct()->getAvailableAmount() - $this->getProduct()->getItemsInCart() ;
    }

    protected function getMinQuantity()
    {
        return 1;
    }

    protected function isPaidMembershipProduct()
    {
        return ProMembershipProducts::getProMembersProductsCount() > 0
            && in_array($this->getProduct()->getProductId(), $this->getIdsProMembershipProducts());
    }

    protected function getIdsProMembershipProducts()
    {
        $ids = [];

        foreach (ProMembershipProducts::getProMembershipProducts() as $proMembershipProduct) {
            $ids[] = $proMembershipProduct->getProductId();
        }

        return $ids;
    }
}