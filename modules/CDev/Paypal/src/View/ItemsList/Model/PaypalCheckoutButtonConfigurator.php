<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\ItemsList\Model;

class PaypalCheckoutButtonConfigurator extends \CDev\Paypal\View\ItemsList\Model\PaypalButton
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'location'     => [
                static::COLUMN_MAIN    => true,
                static::COLUMN_NAME    => static::t('Location'),
                static::COLUMN_ORDERBY => 100,
            ],
            'preview'      => [
                static::COLUMN_NAME    => static::t('Preview'),
                static::COLUMN_CLASS   => 'CDev\Paypal\View\FormField\ButtonPreview',
                static::COLUMN_ORDERBY => 200,
            ],
            'size'         => [
                static::COLUMN_NAME    => static::t('Size'),
                static::COLUMN_CLASS   => 'CDev\Paypal\View\FormField\Inline\ButtonSize',
                static::COLUMN_ORDERBY => 300,
            ],
            'color'        => [
                static::COLUMN_NAME    => static::t('Color'),
                static::COLUMN_CLASS   => 'CDev\Paypal\View\FormField\Inline\ButtonColor',
                static::COLUMN_ORDERBY => 400,
            ],
            'shape'        => [
                static::COLUMN_NAME    => static::t('Shape'),
                static::COLUMN_CLASS   => 'CDev\Paypal\View\FormField\Inline\ButtonShape',
                static::COLUMN_ORDERBY => 500,
            ],
        ];
    }

    /**
     * @return string
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\Payment\Settings';
    }

    /**
     * @return array
     */
    protected function getPlainData()
    {
        $data = parent::getPlainData();

        unset($data['credit']);

        return $data;
    }
}
