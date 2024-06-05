<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\View\ItemsList\Model\Shipping;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Methods extends \XLite\View\ItemsList\Model\Shipping\Methods
{
    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $carrierParam = \XLite\Model\Repo\Shipping\Method::P_CARRIER;

        $config = \XLite\Core\Config::getInstance()->CDev->USPS;
        if (
            !empty($result->{$carrierParam})
            && $result->{$carrierParam} === 'usps'
            && $config->dataProvider === 'pitneyBowes'
        ) {
            $result->{$carrierParam} = 'pb_usps';
        }

        return $result;
    }
}
