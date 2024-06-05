<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Login;

use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;
use CDev\Paypal\Core\PaypalAuthProvider;
use CDev\SocialLogin\Core\AAuthProvider;

/**
 * @Extender\Depend ("CDev\SocialLogin")
 * @ListChild (list="social.login.buttons", zone="customer", weight="20")
 */
class PaypalButton extends \CDev\SocialLogin\View\AButton
{
    /**
     * Widget display name
     */
    public const DISPLAY_NAME = 'PayPal';

    /**
     * Font awesome class
     */
    public const FONT_AWESOME_CLASS = 'fa-paypal';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/login/common.css';
        $list[] = 'modules/CDev/Paypal/login/style.css';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/Paypal/login/controller.js';

        return $list;
    }

    /**
     * Get authentication request url
     *
     * @return string
     */
    public function getAuthRequestUrl()
    {
        $returnUrl = \XLite\Core\Request::getInstance()->fromURL
            ?: \XLite::getController()->getURL();

        $state = get_class(\XLite::getController()) . AAuthProvider::STATE_DELIMITER . urlencode($returnUrl);

        return $this->buildURL(
            'paypal_login',
            '',
            [PaypalAuthProvider::STATE_PARAM_NAME => $state]
        );
    }

    /**
     * Returns an instance of auth provider
     *
     * @return AAuthProvider
     */
    protected function getAuthProvider()
    {
        return PaypalAuthProvider::getInstance();
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/login/button.twig';
    }
}
