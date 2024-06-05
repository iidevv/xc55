<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\PdfPage;

use XCart\Extender\Mapping\Extender;

/**
 * PDF invoice
 * @Extender\Mixin
 */
class Invoice extends \XLite\View\PdfPage\Invoice
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
                'modules/QSL/LoyaltyProgram/order/invoice/parts/earned_points.css',
            ]
        );
    }
}
