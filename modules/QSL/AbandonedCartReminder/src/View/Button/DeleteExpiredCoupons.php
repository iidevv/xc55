<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Button;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Depend ("CDev\Coupons")
 */
class DeleteExpiredCoupons extends \XLite\View\Button\Regular
{
    /**
     * Returns the default button label.
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Delete expired coupons';
    }

    /**
     * Returns the default action for the form.
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'deleteExpiredCoupons';
    }
}
