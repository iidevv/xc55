<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View;

use QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers;

/**
 * Widget that displays order item subtotals.
 */
class OrderItemSubtotal extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    public const PARAM_ORDER_ITEM = 'item';
    public const PARAM_ORDER_CART = 'cart';

    /**
     * Epsilon constant used when comparing float values.
     */
    public const EPS = 0.000000001;

    /**
     * Item surcharges.
     *
     * @var array
     */
    protected $surcharges;

    /**
     * Add widget-specific styles.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file' => $this->getDir() . '/styles.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->isCustomSubtotalWidget() ?
            $this->getDir() . '/body.twig'
            : 'shopping_cart/parts/item.subtotal.twig';
    }

    /**
     * Returns the path to the folder with widget templates.
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/SpecialOffersBase/shopping_cart/item_subtotal';
    }

    /**
     * Define widget parameters.
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ORDER_ITEM => new \XLite\Model\WidgetParam\TypeObject(
                'Item',
                null,
                false,
                '\XLite\Model\OrderItem'
            ),
            static::PARAM_ORDER_CART => new \XLite\Model\WidgetParam\TypeObject(
                'Cart',
                null,
                false,
                '\XLite\Model\Cart'
            ),
        ];
    }

    /**
     * Returns the product option.
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getItem()
    {
        return $this->getParam(self::PARAM_ORDER_ITEM);
    }

    /**
     * Returns the product option.
     *
     * @return \XLite\Model\Cart
     */
    protected function getCart()
    {
        return $this->getParam(self::PARAM_ORDER_CART);
    }

    /**
     * Check if the custom widget should be used for displaying order item subtotals.
     *
     * @return boolean
     */
    protected function isCustomSubtotalWidget()
    {
        return \XLite\Core\Config::getInstance()->QSL->SpecialOffersBase->soffersb_override_tpl;
    }

    /**
     * Check if the item has surcharges.
     *
     * @return boolean
     */
    protected function hasSurcharges()
    {
        return (count($this->getSurcharges()) > 0) && ($this->getDiscount() > static::EPS);
    }

    /**
     * @return boolean
     */
    protected function isFreeItem()
    {
        return $this->getTotal() < static::EPS;
    }

    /**
     * Returns item surcharges that are not included into the item price.
     *
     * @return array
     */
    protected function getSurcharges()
    {
        if (!isset($this->surcharges)) {
            $this->defineSurcharges();
        }

        return $this->surcharges;
    }

    /**
     * Prepares the array with information on surcharges applied on the item.
     *
     * @return void
     */
    protected function defineSurcharges()
    {
        $this->surcharges = [];

        foreach ($this->getItem()->getExcludeSurcharges() as $surcharge) {
            $code = $surcharge->getCode();
            if (!isset($this->surcharges[$code])) {
                $this->surcharges[$code] = [
                    'value'  => abs($surcharge->getValue()),
                    'label'  => $this->getSurchargeLabel($surcharge),
                    'models' => [$surcharge],
                ];
            } else {
                $this->surcharges[$code]['value'] += abs($surcharge->getValue());
                $this->surcharges[$code]['models'][] = $surcharge;
            }
        }
    }

    /**
     * Returns the line item subtotal without any surcharges.
     *
     * @return float
     */
    protected function getSubtotal()
    {
        return $this->getItem()->getSubtotal();
    }

    /**
     * Returns the final line item subtotal including all applies item surcharges.
     *
     * @return float
     */
    protected function getTotal()
    {
        return $this->getItem()->getTotal();
    }

    /**
     * Returns the difference between the original subtotal and the subtotal including surcharges.
     *
     * @return float
     */
    protected function getDiscount()
    {
        return $this->getSubtotal() - $this->getTotal();
    }

    /**
     * Checks if the line item has a discount.
     *
     * @return boolean
     */
    protected function hasDiscount()
    {
        return $this->getDiscount() > static::EPS;
    }

    /**
     * Returns name/label for the surcharge.
     *
     * @param \XLite\Model\OrderItem\Surcharge $surcharge Surcharge.
     *
     * @return string
     */
    protected function getSurchargeLabel(\XLite\Model\OrderItem\Surcharge $surcharge)
    {
        if ($surcharge->getCode() == SpecialOffers::MODIFIER_CODE) {
            $label = $this->t('Special Offer discount');
        } else {
            $label = $this->t('Including X', ['name' => $surcharge->getName()]);
        }

        return $label;
    }

    /**
     * Returns the order currency.
     *
     * @return \XLite\Model\Currency
     */
    protected function getCurrency()
    {
        return $this->getCart()->getCurrency();
    }
}
