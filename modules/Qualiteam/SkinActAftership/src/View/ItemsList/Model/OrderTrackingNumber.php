<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\ItemsList\Model;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use Qualiteam\SkinActAftership\View\FormField\Inline\Select\Select2\ShippingMethodCouriers;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OrderTrackingNumber extends \XLite\View\ItemsList\Model\OrderTrackingNumber
{
    use AftershipTrait;

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns(): array
    {
        $columns = parent::defineColumns();

        $columns['aftership_courier_name'] = [
            static::COLUMN_NAME => static::t('SkinActAftership couriers'),
            static::COLUMN_CLASS => ShippingMethodCouriers::class,
            static::COLUMN_ORDERBY  => 400,
        ];

        $columns['aftership_error'] = [
            static::COLUMN_ORDERBY => 500,
            static::COLUMN_TEMPLATE => $this->getModulePath() . '/aftership_error.twig',
        ];

        $columns['aftership_trackit'] = [
            static::COLUMN_ORDERBY => 600,
            static::COLUMN_TEMPLATE => $this->getModulePath() . '/button/trackit.twig',
        ];

        if (isset($columns['track'])) {
            unset($columns['track']);
        }

        return $columns;
    }
}
