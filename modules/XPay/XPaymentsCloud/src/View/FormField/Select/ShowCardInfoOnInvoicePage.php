<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\FormField\Select;

use XLite\View\FormField\Select\Regular;

class ShowCardInfoOnInvoicePage extends Regular
{
    const SHOW_TO_EVERYONE   = 'Y';
    const SHOW_TO_ADMIN_ONLY = 'A';
    const DO_NOT_SHOW        = 'N';

    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        return [
            self::SHOW_TO_EVERYONE   => 'Show to eligible store back end users (admin, vendor) and to customer',
            self::SHOW_TO_ADMIN_ONLY => 'Show to eligible store back end users (admin, vendor) only',
            self::DO_NOT_SHOW        => 'Don\'t show to anyone',
        ];
    }

}
