<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\FormField\Input;

/**
 * X-Payments Connection key input
 */
class ConnectionKey extends \XLite\View\FormField\Input\Text
{
    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 1024;
    }

    /**
     * getDefaultName
     *
     * @return string
     */
    protected function getDefaultName()
    {
        return 'connection_key';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'X-Payments Connection Key';
    }

}
