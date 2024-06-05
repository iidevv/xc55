<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Logic\BulkEdit;

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

        $position = 100;
        $minimumPurchaseQuantity = [
            'membership_0' => [
                'class'   => 'CDev\Wholesale\Logic\BulkEdit\Field\Product\MinimumPurchaseQuantity',
                'options' => [
                    'label'    => \XLite\Core\Translation::getInstance()->translate('All customers'),
                    'position' => $position,
                ],
            ],
        ];

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Membership')->findAll() as $membership) {
            $position += 100;
            $minimumPurchaseQuantity['membership_' . $membership->getMembershipId()] = [
                'class'   => 'CDev\Wholesale\Logic\BulkEdit\Field\Product\MinimumPurchaseQuantity',
                'options' => [
                    'label'    => $membership->getName(),
                    'position' => $position,
                ],
            ];
        }

        $result['product_inventory']['sections'] = array_replace(
            $result['product_inventory']['sections'] ?? [],
            [
                'minimum_purchase_quantity' => \XLite\Core\Translation::getInstance()->translate('Minimum purchase quantity'),
            ]
        );

        $result['product_inventory']['fields']['minimum_purchase_quantity'] = $minimumPurchaseQuantity;

        return $result;
    }
}
