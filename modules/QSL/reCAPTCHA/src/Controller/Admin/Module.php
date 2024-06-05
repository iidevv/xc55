<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Module settings
 * @Extender\Mixin
 */
class Module extends \XLite\Controller\Admin\Module
{
    /**
     * Return current module options.
     *
     * @return array
     */
    public function getOptions()
    {
        return ($this->getModuleId() === 'QSL-reCAPTCHA')
            ? $this->postprocessGoogleRecaptchaOptions(parent::getOptions())
            : parent::getOptions();
    }

    /**
     * Postprocesses Google reCAPTCHA module settings.
     *
     * @param array $options Options to postprocess
     *
     * @return array
     */
    protected function postprocessGoogleRecaptchaOptions($options)
    {
        $result = [];

        $a = $this->getGoogleRecaptchaOptionAvailability();
        foreach ($options as $option) {
            $name = $option->getName();
            if (!isset($a[$name]) || $a[$name]) {
                $result[] = $option;
            }
        }

        return $result;
    }

    /**
     * Information on which Google reCAPTCHA module settings should be displayed
     * or hidden.
     *
     * @return array
     */
    protected function getGoogleRecaptchaOptionAvailability()
    {
        return [
            'google_recaptcha_contact'   => (
                class_exists('\CDev\ContactUs\Core\ReCaptcha')
                || \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('QSL\AdvancedContactUs')
            ),
            'google_recaptcha_vendor'    => class_exists('\XC\MultiVendor\Controller\Admin\RegisterVendor'),
            'recaptcha_vendor_min_score' => class_exists('\XC\MultiVendor\Controller\Admin\RegisterVendor'),
            'recaptcha_vendor_fallback'  => class_exists('\XC\MultiVendor\Controller\Admin\RegisterVendor'),
        ];
    }
}
