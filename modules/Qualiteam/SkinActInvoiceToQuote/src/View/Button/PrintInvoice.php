<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActInvoiceToQuote\View\Button;

use Qualiteam\SkinActInvoiceToQuote\Main;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class print invoice
 * @Extender\Mixin
 */
class PrintInvoice extends \XLite\View\Button\PrintInvoice
{
    /**
     * Get default label
     * todo: move translation here
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return Main::isShowCustomLabel($this->getOrder())
            ? static::t('SkinActInvoiceToQuote print quote')
            : parent::getDefaultLabel();
    }
}