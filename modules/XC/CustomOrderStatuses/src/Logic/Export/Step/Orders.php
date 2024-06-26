<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Orders
 * @Extender\Mixin
 */
class Orders extends \XLite\Logic\Export\Step\Orders
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        foreach (Database::getRepo('XLite\Model\Order\Status\PaymentTranslation')->getUsedLanguageCodes() as $code) {
            foreach ($columns as $name => $column) {
                $columns['paymentStatusLabel_' . $code] = [static::COLUMN_GETTER => 'getPaymentStatusLabelColumnValue'];
            }
        }

        foreach (Database::getRepo('XLite\Model\Order\Status\ShippingTranslation')->getUsedLanguageCodes() as $code) {
            foreach ($columns as $name => $column) {
                $columns['shippingStatusLabel_' . $code] = [static::COLUMN_GETTER => 'getShippingStatusLabelColumnValue'];
            }
        }

        return $columns;
    }

    /**
     * Get column value for 'paymentStatus' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getPaymentStatusLabelColumnValue(array $dataset, $name, $i)
    {
        $status = $dataset['model']->getPaymentStatus();

        return $status
            ? $status->getTranslation(substr($name, -2))->getName()
            : '';
    }

    /**
     * Get column value for 'shippingStatus' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getShippingStatusLabelColumnValue(array $dataset, $name, $i)
    {
        $status = $dataset['model']->getShippingStatus();

        return $status
            ? $status->getTranslation(substr($name, -2))->getName()
            : '';
    }
}
