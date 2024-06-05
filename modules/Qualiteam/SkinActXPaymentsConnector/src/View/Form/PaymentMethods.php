<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Form;

/**
 * Test module form
 *
 */
class PaymentMethods extends \Qualiteam\SkinActXPaymentsConnector\View\Form\Settings
{
    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_payment_methods';
    }
}
