<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Form;

/**
 * Customer box
 */
class CustomerBox extends \XLite\View\Form\AForm
{
    /**
     * Widget parameter names
     */
    public const PARAM_PRODUCT = 'product';

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product',
                null,
                false,
                'XLite\Model\Product'
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'add_back2stock_record';
    }

    /**
     * @inheritdoc
     */
    protected function getFormParams()
    {
        return parent::getFormParams() +
            [
                'product_id' => $this->getParam(static::PARAM_PRODUCT)->getProductId(),
            ];
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultClassName()
    {
        return 'notify-me-form use-inline-errors';
    }
}
