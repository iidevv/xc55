<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\MarketPrice\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $currency = \XLite::getInstance()->getCurrency();
        $currencySymbol = $currency->getCurrencySymbol(false);

        $schema = static::compose(
            $schema,
            [
                'prices_and_inventory' => [
                    'price' => [
                        'market_price' => [
                            'label'             => static::t('Market price'),
                            'type'              => 'XLite\View\FormModel\Type\SymbolType',
                            'symbol'            => $currencySymbol,
                            'inputmask_pattern' => [
                                'alias'      => 'xcdecimal',
                                'prefix'     => '',
                                'rightAlign' => false,
                                'digits'     => $currency->getE(),
                            ],
                            'constraints'       => [
                                'Symfony\Component\Validator\Constraints\GreaterThanOrEqual' => [
                                    'value'   => 0,
                                    'message' => static::t('Minimum value is X', ['value' => 0])
                                ]
                            ],
                            'position'          => 200,
                        ]
                    ]
                ]
            ]
        );

        return $schema;
    }
}
