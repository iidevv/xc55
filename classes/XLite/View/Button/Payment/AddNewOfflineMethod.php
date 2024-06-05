<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Payment;

use XLite\Core\Converter;
use XLite\View\Button\Link;

/**
 * Add new offline payment method popup button
 */
class AddNewOfflineMethod extends Link
{
    public function getDefaultLabel(): string
    {
        return 'Add new offline method';
    }

    protected function getLocationURL(): string
    {
        return Converter::buildURL('add_new_offline_method');
    }

    protected function getClass(): string
    {
        return parent::getClass() . ' add-new-offline-method';
    }
}
