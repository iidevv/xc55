<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping\Popup;

/**
 * Shipping custom rates in the popup
 */
class Offline extends \XLite\View\ItemsList\Model\Shipping\Popup\Carriers
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
     * Returns a list of CSS classes (separated with a space character) to be attached to the items
     * list
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' shipping-rates';
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowAddButton(\XLite\Model\Shipping\Method $method)
    {
        return !$method->isAdded();
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowRemoveButton(\XLite\Model\Shipping\Method $method)
    {
        return true;
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowHandlingFee(\XLite\Model\Shipping\Method $method)
    {
        return true;
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowTaxClass(\XLite\Model\Shipping\Method $method)
    {
        return true;
    }
}
