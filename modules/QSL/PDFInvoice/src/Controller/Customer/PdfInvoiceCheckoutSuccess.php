<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\Controller\Customer;

/**
 * PDF invoice downloader
 */
class PdfInvoiceCheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess
{
    /**
     * Start downloading PDF invoice
     *
     * @return void
     */
    protected function doNoAction()
    {
        \QSL\PDFInvoice\Core\DOMPDF::getInstance()->streamPDFInvoice(
            [$this->getOrder()->getOrderId()],
            $this->getCurrentLanguage()
        );
    }
}
