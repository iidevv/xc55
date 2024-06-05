<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * PDF invoice downloader
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
abstract class PdfInvoiceCheckoutSuccessMultiVendor extends \QSL\PDFInvoice\Controller\Customer\PdfInvoiceCheckoutSuccess
{
    /**
     * Start downloading PDF invoice
     *
     * @return void
     */
    protected function doNoAction()
    {
        \QSL\PDFInvoice\Core\DOMPDF::getInstance()->streamPDFInvoice(
            array_map(static function (\XLite\Model\Order $order) {
                return $order->getOrderId();
            }, $this->getOrder()->getChildren()->toArray()),
            $this->getCurrentLanguage()
        );
    }
}
