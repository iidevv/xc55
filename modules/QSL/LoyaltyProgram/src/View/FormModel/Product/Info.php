<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated product edit form.
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

        $schema['prices_and_inventory']['reward_points_box'] = [
            'label'       => static::t('Automatic reward points'),
            'description' => static::t('Configure whether reward points for this product should be calculated from the price automatically, or are defined manually.'),
            'type'        => 'XLite\View\FormModel\Type\Base\CompositeType',
            'fields'      => [
                'auto_reward_points' => [
                    'type'     => 'XLite\View\FormModel\Type\SwitcherType',
                    'position' => 100,
                ],
                'reward_points'      => [
                    'label'             => static::t('Reward points'),
                    'type'              => 'XLite\View\FormModel\Type\PatternType',
                    'inputmask_pattern' => [
                        'alias'      => 'integer',
                        'rightAlign' => false,
                    ],
                    'show_when'         => [
                        'prices_and_inventory' => [
                            'reward_points_box' => [
                                'auto_reward_points' => false,
                            ],
                        ],
                    ],
                    'position'          => 200,
                ],
            ],
            'position'    => 350,
        ];

        return $schema;
    }
}
