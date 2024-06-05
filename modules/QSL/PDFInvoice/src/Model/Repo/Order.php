<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Order repository
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    /**
     * We remove invoices before delete process
     *
     * @param \XLite\Model\AEntity $entity Entity to detach
     *
     * @return void
     */
    protected function performDelete(\XLite\Model\AEntity $entity)
    {
        \QSL\PDFInvoice\Core\DOMPDF::getInstance()->clearPDFInvoices();

        parent::performDelete($entity);
    }

    /**
     * We remove invoices before update process
     *
     * @param \XLite\Model\AEntity $entity Entity to use
     * @param array                $data   Data to save OPTIONAL
     *
     * @return void
     */
    protected function performUpdate(\XLite\Model\AEntity $entity, array $data = [])
    {
        \QSL\PDFInvoice\Core\DOMPDF::getInstance()->clearPDFInvoices();

        parent::performUpdate($entity, $data);
    }
}
