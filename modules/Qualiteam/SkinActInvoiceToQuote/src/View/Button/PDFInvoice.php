<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActInvoiceToQuote\View\Button;

use Qualiteam\SkinActInvoiceToQuote\Main;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class pdf invoice
 * @Extender\Mixin
 */
class PDFInvoice extends \QSL\PDFInvoice\View\Button\PDFInvoice
{
    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return Main::isShowCustomLabel($this->getOrder())
            ? static::t('SkinActInvoiceToQuote open pdf quote')
            : parent::getDefaultLabel();
    }
}