<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * Import products
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products
{
    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'PRODUCT-REWARD-POINTS-FMT' => 'Wrong format of the rewardPoints field',
            ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['rewardPoints'] = [];

        return $columns;
    }

    /**
     * Verify 'rewardPoints' value.
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyRewardPoints($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !preg_match('/^(\d+|auto)$/', $value)) {
            $this->addWarning('PRODUCT-REWARD-POINTS-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Import 'rewardPoints' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     */
    protected function importRewardPointsColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if (($value != '') && ($value != 'auto')) {
            $model->setAutoRewardPoints(false);
            $model->setRewardPoints(intval($value));
        } else {
            $model->setAutoRewardPoints(true);
            $model->setRewardPoints(0);
        }
    }
}
