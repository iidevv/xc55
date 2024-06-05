<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model\Shipping;

use XCart\Extender\Mapping\Extender;

/**
 * Shipping method model
 * @Extender\Mixin
 */
class Method extends \XLite\Model\Shipping\Method
{
    /**
     * get Shipping Method name
     * for Canada Post add '(Canada Post)' (except admin area, shipping methods page)
     *
     * @return string
     */
    public function getName()
    {
        $name = parent::getName();

        if ($this->getProcessor() == 'capost' && !(\XLite::isAdminZone() || \XLite::getController() instanceof \XLite\Controller\Admin\ShippingMethods)) {
            $name = 'Canada Post ' . $name;
        }

        return $name;
    }
}
