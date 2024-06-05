<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Settings\PaymentMethods;

use Qualiteam\SkinActXPaymentsConnector\Core\Settings;
use Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient;
use Qualiteam\SkinActXPaymentsConnector\View\Settings\ASettings;

/**
 * Import payment methods
 */
class Import extends ASettings
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/payment_methods/import.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && XPaymentsClient::getInstance()->isModuleConfigured();
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
