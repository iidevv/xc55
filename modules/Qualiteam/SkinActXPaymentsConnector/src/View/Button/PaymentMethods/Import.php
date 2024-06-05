<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Button\PaymentMethods;

use XLite\View\Button\Regular;

/**
 * Import payment methods
 */
class Import extends Regular
{
    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'import';
    }

}

