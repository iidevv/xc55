<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Event listener
 *
 * @Extender\Mixin
 */
abstract class EventListener extends \XLite\Core\EventListener implements \XLite\Base\IDecorator
{
    /**
     * Get listeners
     *
     * @return array 
     */
    protected function getListeners()
    {
        return parent::getListeners() + array(
            'clearCreditCardsData' => array(\XPay\XPaymentsCloud\Core\EventListener\ClearCCData::class)
        );
    }
}
