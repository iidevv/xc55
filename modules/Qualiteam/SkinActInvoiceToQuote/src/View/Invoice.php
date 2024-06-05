<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActInvoiceToQuote\View;

use Qualiteam\SkinActInvoiceToQuote\Main;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class invoice
 * @Extender\Mixin
 */
class Invoice extends \XLite\View\Invoice
{
    /**
     * Returns invoice title
     *
     * @return string
     */
    protected function getInvoiceTitle()
    {
        return Main::isShowCustomLabel($this->getOrder())
            ? static::t('SkinActInvoiceToQuote quote X', ['id' => $this->getOrder()->getOrderNumber()])
            : parent::getInvoiceTitle();
    }
}