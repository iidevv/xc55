<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\View\Form;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class CustomerBox extends \QSL\BackInStock\View\Form\CustomerBox
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

        $this->widgetParams += [
            static::PARAM_VARIANT => new \XLite\Model\WidgetParam\TypeObject(
                'Variant',
                null,
                false,
                'XC\ProductVariants\Model\ProductVariant'
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'add_back2stock_variant_record';
    }

    /**
     * @inheritdoc
     */
    protected function getFormParams()
    {
        $variant = $this->getParam(static::PARAM_VARIANT);

        if (!$variant) {
            return parent::getFormParams();
        }

        return parent::getFormParams() +
            [
                'variant_id' => $variant->getVariantId(),
            ];
    }
}
