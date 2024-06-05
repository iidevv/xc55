<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XC\Concierge\Core\Mediator;
use XC\Concierge\Core\Track\Track;

/**
 * @Extender\Mixin
 */
abstract class PaymentMethodSelection extends \XLite\Controller\Admin\PaymentMethodSelection
{
    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearchItemsList()
    {
        parent::doActionSearchItemsList();

        Mediator::getInstance()->addMessage(new Track(
            'Payment Method Search',
            [
                'Search Query' => \XLite\Core\Request::getInstance()->substring,
                'Search Country' => \XLite\Core\Request::getInstance()->country,
            ]
        ));
    }
}
