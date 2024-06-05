<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View;

use QSL\BackInStock\Main;

/**
 * Customer box
 */
class CustomerBox extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    public const PARAM_PRODUCT = 'product';

    /**
     * Already created record flag
     *
     * @var boolean
     */
    protected $alreadyCreated;

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $product = \XLite::getController() instanceof \XLite\Controller\Customer\Product
            ? \XLite::getController()->getProduct()
            : null;

        $this->widgetParams += [
            static::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product',
                $product,
                false,
                'XLite\Model\Product'
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/BackInStock/customer_box.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/QSL/BackInStock/customer_box.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        if (Main::isCurrentSkin('XC-CrispWhiteSkin')) {
            $list[] = [
                'file'  => 'modules/QSL/BackInStock/modules/XC/CrispWhiteSkin/customer_box.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ];
        }

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        $config = \XLite\Core\Config::getInstance()->QSL->BackInStock;

        return parent::isVisible()
            && ($config->allowStockNotification || $config->allowPriceNotification)
            && $this->getProduct();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/BackInStock/customer_box.twig';
    }

    /**
     * Get container tag attributes
     *
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        return [];
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * Get email
     *
     * @return string
     */
    protected function getEmail()
    {
        return \XLite\Core\Auth::getInstance()->isLogged()
            ? \XLite\Core\Auth::getInstance()->getProfile()->getLogin()
            : '';
    }

    /**
     * Get default quantity
     *
     * @return integer
     */
    protected function getDefaultQuantity()
    {
        return 1;
    }

    /**
     * Get min. quantity
     *
     * @return integer
     */
    protected function getMinQuantity()
    {
        return 1;
    }

    /**
     * Get default price
     *
     * @return float
     */
    protected function getDefaultPrice()
    {
        return $this->getProduct()->getPrice();
    }

    /**
     * Get max. percent discount
     *
     * @return float
     */
    protected function getMaxPercent()
    {
        return $this->getProduct()->getMaxPriceDiscountNotify();
    }

    /**
     * Get min price
     *
     * @return float
     */
    protected function getMinPrice()
    {
        return $this->getProduct()->getMaxPriceDiscountNotify() > 0
            ? $this->getProduct()->getPrice() * (100 - $this->getProduct()->getMaxPriceDiscountNotify())
            : 0;
    }

    /**
     * Stock notifications enabled
     *
     * @return boolean
     */
    protected function isStockEnabled()
    {
        return $this->getProduct()->isBackInStockAllowed();
    }

    /**
     * Stock quantity specification enabled
     *
     * @return boolean
     */
    protected function isStockSpecificationEnabled()
    {
        return \XLite\Core\Config::getInstance()->QSL->BackInStock->allowSpecifyQuantity;
    }

    /**
     * Price notifications enabled
     *
     * @return boolean
     */
    protected function isPriceEnabled()
    {
        return $this->getProduct()->isPriceDropAllowed();
    }

    /**
     * Price specification enabled
     *
     * @return boolean
     */
    protected function isPriceSpecificationEnabled()
    {
        return \XLite\Core\Config::getInstance()->QSL->BackInStock->allowSpecifyPrice;
    }

    /**
     * Enabled both (stock and price)?
     *
     * @return boolean
     */
    protected function isEnabledBoth()
    {
        return $this->isStockEnabled() && $this->isPriceEnabled();
    }
}
