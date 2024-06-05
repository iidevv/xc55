<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\Controller\Admin;

/**
 * PDF invoice downloader
 */
class PdfInvoice extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return (
            parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders')
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('[vendor] manage orders')
        ) && $this->getOrderId();
    }

    /**
     * Order numbers is sent as sequence of order numbers with ',' delimeter
     *
     * @return array
     */
    protected function getOrderId()
    {
        return explode(',', \XLite\Core\Request::getInstance()->order_id);
    }

    /**
     * Start downloading PDF invoice
     *
     * @return void
     */
    protected function doNoAction()
    {
        \QSL\PDFInvoice\Core\DOMPDF::getInstance()->streamPDFInvoice(
            $this->getOrderId(),
            $this->getCurrentLanguage()
        );
    }

    public function getOrder()
    {
        return null;
    }
}
