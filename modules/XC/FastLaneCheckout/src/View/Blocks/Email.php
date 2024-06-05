<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View\Blocks;

use XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
class Email extends \XLite\View\Checkout\AAddressBlock
{
    /**
     * Check - password field is visible or not
     *
     * @return boolean
     */
    protected function isPasswordVisible()
    {
        return false;
    }

    /**
     * Check - email field is visible or not
     *
     * @return boolean
     */
    protected function isEmailVisible()
    {
        return true;
    }

    /**
     * Get address info model
     *
     * @return \XLite\Model\Address
     */
    protected function getAddressInfo()
    {
        $profile = $this->getCart()->getProfile();

        return $profile ? $profile->getShippingAddress() : null;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = [];

        $list[] = [
            'file'  => FastLaneCheckout\Main::getSkinDir() . 'blocks/email/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = [];

        $list[] = FastLaneCheckout\Main::getSkinDir() . 'blocks/email/email.js';

        return $list;
    }

    /**
     * @return void
     */
    protected function getDefaultTemplate()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/email/template.twig';
    }

    /**
     * Returns email value
     * @return string
     */
    protected function getEmailValue()
    {
        return $this->getFieldValue('email', true);
    }
}
