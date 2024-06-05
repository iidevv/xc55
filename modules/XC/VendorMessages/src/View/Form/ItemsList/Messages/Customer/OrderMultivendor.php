<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Form\ItemsList\Messages\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Customer order messages
 *
 * @Extender\Mixin
 * @Extender\After ("XC\VendorMessages")
 * @Extender\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XC\VendorMessages\View\Form\ItemsList\Messages\Customer\Order
{
    /**
     * @inheritdoc
     */
    protected function getDefaultClassName()
    {
        return parent::getDefaultClassName()
            . (\XC\VendorMessages\Main::isVendorAllowedToCommunicate() ? ' multivendor-enabled' : '');
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();
        $list['recipient_id'] = \XLite\Core\Request::getInstance()->recipient_id;

        return $list;
    }
}
