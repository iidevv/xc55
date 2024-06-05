<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\Checkout;

use Includes\Utils\Module\Manager;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * GdprConsent
 *
 * @ListChild (list="checkout.review.selected.placeOrder", weight="300")
 */
class GdprConsent extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/GDPR/checkout/gdpr_consent.twig';
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $files = ['modules/XC/GDPR/checkout/gdpr_consent.js'];

        if ($this->isAmazonHackRequired()) {
            $files[] = 'modules/XC/GDPR/checkout/amazon_hack.js';
        } elseif ($this->isIframeHackRequired()) {
            $files[] = 'modules/XC/GDPR/checkout/iframe_hack.js';
        }

        return $files;
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !Auth::getInstance()->isUserGdprConsent();
    }

    /**
     * @return bool
     */
    protected function isAmazonHackRequired()
    {
        return \XLite::getController()->getTarget() === 'amazon_checkout';
    }

    /**
     * @return bool
     */
    protected function isIframeHackRequired()
    {
        /** @var \XLite\Model\Cart $cart */
        $cart = \XLite::getController()->getCart();

        return $cart && Manager::getRegistry()->isModuleEnabled('CDev-XPaymentsConnector');
    }
}
