<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\ItemsList\Messages\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Customer order messages
 *
 * @Extender\Mixin
 * @Extender\After ("XC\VendorMessages")
 * @Extender\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XC\VendorMessages\View\ItemsList\Messages\Customer\Order
{
    /**
     * @inheritdoc
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses()
            . (\XC\VendorMessages\Main::isVendorAllowedToCommunicate() ? ' multivendor-enabled' : '');
    }

    /**
     * @inheritdoc
     */
    protected function getWidgetParameters()
    {
        return parent::getWidgetParameters() + [
            'recipient_id' => intval(\XLite\Core\Request::getInstance()->recipient_id),
        ];
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $initialize = !isset($this->commonParams);

        $this->commonParams = parent::getCommonParams();

        if ($initialize) {
            $this->commonParams += [
                'recipient_id' => intval(\XLite\Core\Request::getInstance()->recipient_id),
            ];
        }

        return $this->commonParams;
    }

    /**
     * Get order items
     *
     * @return \XLite\Model\OrderItem[]
     */
    protected function getItems()
    {
        return $this->getCurrentThreadOrder()->getItems();
    }
}
