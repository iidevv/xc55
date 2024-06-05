<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\View\Button;

use XCart\Extender\Mapping\ListChild;

/**
 * 'PDF invoice' button widget
 *
 * @ListChild (list="page.tabs.after", zone="admin", weight="1")
 */
class PDFInvoice extends \XLite\View\Button\Link
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return static::getAllowedTargetsButton();
    }

    /**
     * Define the specific targets for the button
     *
     * @return array
     */
    protected static function getAllowedTargetsButton()
    {
        return ['order'];
    }

    /**
     * The specific button styles are defined
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/PDFInvoice/button.css';

        return $list;
    }

    /**
     * Defines the default location path
     *
     * @return null|string
     */
    protected function getDefaultLocation()
    {
        return $this->buildURL(
            'pdf_invoice',
            '',
            [
                'order_id' => $this->getOrderId(),
            ]
        );
    }

    /**
     * Returns the order number from the controllers
     *
     * @return string
     */
    protected function getOrderId()
    {
        return $this->getOrder()->getOrderId();
    }

    /**
     * Get default CSS class name
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'button print-invoice pdf-invoice';
    }

    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Open PDF invoice';
    }
}
