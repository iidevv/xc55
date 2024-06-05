<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\Core\Mail\Order;

use XCart\Extender\Mapping\Extender;

/**
 * DOM PDF
 * @Extender\Mixin
 */
abstract class AOrder extends \XLite\Core\Mail\Order\AOrder
{
    public function isAttachPdfInvoice()
    {
        return \XLite\Core\Config::getInstance()->QSL->PDFInvoice->sendPDFAttachment || parent::isAttachPdfInvoice();
    }
}
