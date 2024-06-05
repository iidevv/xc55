<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Logic\BulkEdit;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\BulkEditing")
 */
class Scenario extends \XC\BulkEditing\Logic\BulkEdit\Scenario
{
    /**
     * @return array
     */
    protected static function defineScenario()
    {
        $result = parent::defineScenario();

        $result['coupons'] = [
            'title'     => \XLite\Core\Translation::getInstance()->translate('Coupons'),
            'formModel' => 'CDev\Coupons\View\FormModel\BulkEdit\Product\Coupons',
            'view'      => 'CDev\Coupons\View\ItemsList\BulkEdit\Product\Coupons',
            'DTO'       => 'CDev\Coupons\Model\DTO\BulkEdit\Product\Coupons',
            'step'      => 'XC\BulkEditing\Logic\BulkEdit\Step\Product',
            'fields'    => [
                'default' => [
                    'coupons' => [
                        'class'   => 'CDev\Coupons\Logic\BulkEdit\Field\Product\Coupons',
                        'options' => [
                            'position' => 100,
                        ],
                    ],
                ],
            ],
        ];

        return $result;
    }
}
