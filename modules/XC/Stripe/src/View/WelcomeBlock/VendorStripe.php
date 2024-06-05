<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\View\WelcomeBlock;

use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;
use XC\Stripe\Main;

/**
 * Stripe Connect banner
 *
 * @ListChild (list="dashboard-center", zone="admin", weight="15")
 * @Extender\Depend ("XC\MultiVendor")
 */
class VendorStripe extends \XLite\View\AWelcomeBlock
{
    /**
     * Add widget specific CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Stripe/welcome_block/vendor_stripe';
    }

    protected function getInnerViewList()
    {
        return 'welcome-block.vendor-stripe';
    }

    protected function getBlockName()
    {
        return 'vendor-stripe';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return !$this->isRootAccess()
            && Auth::getInstance()->isVendor()
            && $this->isNotHiddenByUser()
            && $this->isStripeConnectConfigured()
            && !$this->isVendorConnectedToStripe();
    }

    /**
     * Check if the current user has connected to Stripe
     *
     * @return boolean
     */
    protected function isVendorConnectedToStripe()
    {
        return $this->getProfile() ? $this->getProfile()->getStripeSellerAccountId() : false;
    }

    /**
     * @return bool
     */
    protected function isStripeConnectConfigured()
    {
        return Main::getStripeConnectMethod()->isConfigured();
    }

    /**
     * Get financial tab url
     */
    public function getFinancialTabURL()
    {
        return \XLite\Core\Converter::buildURL('financialInfo');
    }

    public function getProfile()
    {
        return Auth::getInstance()->getProfile();
    }
}
