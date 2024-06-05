<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Settings;

use Qualiteam\SkinActXPaymentsConnector\Core\Settings;

/**
 * Settings payment methods
 */
class PaymentMethods extends ASettings
{

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/payment_methods/list.twig';
    }

    /**
     * List of tabs/pages where this setting should be displayed
     *
     * @return array
     */
    public function getPages()
    {
        return [Settings::PAGE_PAYMENT_METHODS];
    }
}
