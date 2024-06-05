<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Invoice widget
 *
 * @ListChild (list="order.children", weight="30", zone="admin")
 */
class PackingSlip extends \XLite\View\PackingSlip
{
    /**
     * Widget parameter names
     */
    public const PARAM_PARCEL = 'parcel';

    /**
     * Get order
     *
     * @return \XC\CanadaPost\Model\Order\Parcel
     */
    public function getParcel()
    {
        return $this->getParam(self::PARAM_PARCEL);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PARCEL => new \XLite\Model\WidgetParam\TypeObject(
                'Parcel',
                null,
                false,
                'XC\CanadaPost\Model\Order\Parcel'
            ),
        ];
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getParcel();
    }

    /**
     * Returns packing slip title
     *
     * @return string
     */
    protected function getPackingSlipParcelNo()
    {
        return $this->getParcel()->getNumber();
    }

    /**
     * Returns order items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    protected function getOrderItems()
    {
        return array_map(static function ($item) {
            return $item->getOrderItem();
        }, $this->getParcel()->getItems()->toArray());
    }
}
