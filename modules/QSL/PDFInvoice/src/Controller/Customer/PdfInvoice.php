<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\Controller\Customer;

/**
 * PDF invoice downloader
 */
class PdfInvoice extends \XLite\Controller\Customer\Base\Order
{
    /**
     * Start downloading PDF invoice
     *
     * @return void
     */
    protected function doNoAction()
    {
        \QSL\PDFInvoice\Core\DOMPDF::getInstance()->streamPDFInvoice(
            [$this->getOrderId()],
            $this->getCurrentLanguage()
        );
    }
}
