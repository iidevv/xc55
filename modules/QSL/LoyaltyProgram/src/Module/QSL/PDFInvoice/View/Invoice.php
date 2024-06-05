<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Module\QSL\PDFInvoice\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\PDFInvoice")
 */
class Invoice extends \QSL\PDFInvoice\View\Invoices
{
    /**
     * CSS for PDF page
     *
     * @return array
     */
    public function getPdfStylesheets()
    {
        return array_merge(
            parent::getPdfStylesheets(),
            [
                'modules/QSL/LoyaltyProgram/order/invoice/parts/earned_points.css'
            ]
        );
    }
}
