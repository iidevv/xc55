<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActInvoiceToQuote\View\PdfPage;

use Qualiteam\SkinActInvoiceToQuote\Main;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class invoice
 * @Extender\Mixin
 */
class Invoice extends \XLite\View\PdfPage\Invoice
{
    /**
     * Returns PDF document title
     *
     * @return string
     */
    public function getDocumentTitle()
    {
        $title = parent::getDocumentTitle();

        if (Main::isShowCustomLabel($this->getOrder())) {
            $title = $this->getOrder()
                ? static::t('SkinActInvoiceToQuote order number quote', [
                    'number' => $this->getOrder()->getPrintableOrderNumber()
                ])
                : static::t('SkinActInvoiceToQuote order quote');
        }

        return $title;
    }
}