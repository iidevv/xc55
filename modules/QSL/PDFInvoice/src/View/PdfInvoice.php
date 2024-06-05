<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\View;

use XCart\Extender\Mapping\Extender;

/**
 * Pdf test page
 * @Extender\Mixin
 */
class PdfInvoice extends \XLite\View\PdfPage\Invoice
{
    public function getPdfStylesheets()
    {
        return array_merge(
            parent::getPdfStylesheets(),
            [
                'modules/QSL/PDFInvoice/style.less',
            ]
        );
    }
}
