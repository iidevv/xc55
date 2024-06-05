<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActInvoiceToQuote\Controller\Customer;

use Qualiteam\SkinActInvoiceToQuote\Main;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class invoice
 * @Extender\Mixin
 */
class Invoice extends \XLite\Controller\Customer\Invoice
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return Main::isShowCustomLabel($this->getOrder())
            ? static::t(
                'SkinActInvoiceToQuote quote #{{orderId}}, {{time}}',
                [
                    'orderId' => $this->getOrderNumber(),
                    'time'    => \XLite\Core\Converter::getInstance()->formatTime($this->getOrder()->getDate())
                ]
            ) : parent::getTitle();
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return Main::isShowCustomLabel($this->getOrder())
            ? static::t('SkinActInvoiceToQuote quote')
            : parent::getLocation();
    }
}