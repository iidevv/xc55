<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class CustomerBox extends \QSL\BackInStock\View\CustomerBox
{
    /**
     * Widget parameter names
     */
    public const PARAM_VARIANT = 'variant';

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $variant = \XLite::getController() instanceof \XLite\Controller\Customer\Product
            ? \XLite::getController()->getProductVariant()
            : null;

        $this->widgetParams += [
            static::PARAM_VARIANT => new \XLite\Model\WidgetParam\TypeObject(
                'Variant',
                $variant,
                false,
                'XC\ProductVariants\Model\ProductVariant'
            ),
        ];
    }

    /**
     * Get product variant
     *
     * @return \XC\ProductVariants\Model\ProductVariant
     */
    protected function getProductVariant()
    {
        return $this->getParam(static::PARAM_VARIANT);
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/BackInStock/modules/XC/ProductVariants/customer_box.twig';
    }

    /**
     * Stock notifications enabled
     *
     * @return boolean
     */
    protected function isStockEnabled()
    {
        $variant = $this->getProductVariant();

        if ($variant) {
            return $variant->isBackInStockAllowed();
        }

        return parent::isStockEnabled();
    }

    /**
     * Price notifications enabled
     *
     * @return boolean
     */
    protected function isPriceEnabled()
    {
        $variant = $this->getProductVariant();

        if ($variant) {
            return $variant->isPriceDropAllowed();
        }

        return parent::isPriceEnabled();
    }
}
