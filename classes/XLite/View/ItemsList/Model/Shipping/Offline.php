<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping;

use XLite\View\Pager\Infinity;

/**
 * Shipping custom rates
 */
class Offline extends \XLite\View\ItemsList\Model\Shipping\Carriers
{
    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Shipping\Method::P_PROCESSOR} = 'offline';

        return $result;
    }

    /**
     * Returns list of zones as a string
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    protected function getZonesList($method)
    {
        $result = '';

        $zones = [];
        foreach ($method->getShippingMarkups() as $markup) {
            if (
                $markup
                && $markup->getZone()
                && !in_array($markup->getZone()->getZoneName(), $zones, true)
            ) {
                $zones[] = $markup->getZone()->getZoneName();
            }
        }

        if (count($zones)) {
            sort($zones);
            $result = implode(', ', $zones);
        }

        return $result;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' shipping-rates';
    }

    /**
     * Get right actions templates
     *
     * @return array
     */
    protected function getRightActions()
    {
        $list = parent::getRightActions();
        array_unshift($list, 'items_list/model/table/shipping/carriers/action.edit.twig');

        return $list;
    }

    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    /**
     * @return bool
     */
    public function isCrossIcon()
    {
        return true;
    }

    protected function getPagerClass()
    {
        return Infinity::class;
    }
}
