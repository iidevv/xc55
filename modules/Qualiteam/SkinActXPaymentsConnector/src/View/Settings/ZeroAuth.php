<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Settings;

use Qualiteam\SkinActXPaymentsConnector\Core\Settings;

/**
 * Zero-dolar authrization (card setup) settings
 *
 */
class ZeroAuth extends ASettings
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/zero_auth.twig';
    }

    /**
     * List of tabs/pages where this setting should be displayed
     *
     * @return array
     */
    public function getPages()
    {
        return [Settings::PAGE_ZERO_AUTH];
    }
}
