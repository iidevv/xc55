<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\View\Button;

/**
 * 'PDF invoice' button widget (on checkout success page)
 */
class PDFInvoiceCheckoutSuccess extends PDFInvoice
{
    /**
     * Define the specific targets for the button
     *
     * @return array
     */
    protected static function getAllowedTargetsButton()
    {
        return ['checkoutSuccess'];
    }

    /**
     * Defines the default location path
     *
     * @return null|string
     */
    protected function getDefaultLocation()
    {
        return $this->buildURL(
            'pdf_invoice_checkout_success',
            '',
            [
                'order_id' => $this->getOrder()->getOrderId(),
            ]
        );
    }
}
