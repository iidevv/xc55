<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Orders extends \XLite\Logic\Export\Step\Orders
{
    protected function getModelDatasets(\XLite\Model\AEntity $model)
    {
        $datasets = parent::getModelDatasets($model);

        $datasets = $this->distributeDatasetModel(
            $datasets,
            'coupon',
            $model->getUsedCoupons()
        );

        return $datasets;
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array_merge(parent::defineColumns(), [
            'couponCode'   => [static::COLUMN_MULTIPLE => true],
            'couponType'   => [static::COLUMN_MULTIPLE => true],
            'couponAmount' => [static::COLUMN_MULTIPLE => true],
        ]);
    }

    /**
     * Get column value
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getCouponCodeColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['coupon'])
            ? ''
            : $this->getColumnValueByName($dataset['coupon'], 'code');
    }

    /**
     * Get column value
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getCouponTypeColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['coupon'])
            ? ''
            : $this->getColumnValueByName($dataset['coupon'], 'type');
    }

    /**
     * Get column value
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getCouponAmountColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['coupon'])
            ? ''
            : $this->getColumnValueByName($dataset['coupon'], 'value');
    }
}
