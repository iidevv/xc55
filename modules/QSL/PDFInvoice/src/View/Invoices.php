<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\View;

/**
 * Pdf test page
 */
class Invoices extends \XLite\View\PdfPage\Invoice
{
    public const PARAM_ORDERIDS = 'orderids';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ORDERIDS => new \XLite\Model\WidgetParam\TypeCollection('OrderIds', []),
        ];
    }

    /**
     * Returns PDF document title
     *
     * @return string
     */
    public function getDocumentTitle()
    {
        return count($this->getOrders()) > 1
            ? 'Order invoices'
            : parent::getDocumentTitle();
    }

    protected function getOrders()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Order')->findByIds($this->getParam(self::PARAM_ORDERIDS));
    }

    public function getDefaultTemplate()
    {
        return 'modules/QSL/PDFInvoice/page.twig';
    }
}
