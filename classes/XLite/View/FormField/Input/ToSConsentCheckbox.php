<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

class ToSConsentCheckbox extends \XLite\View\FormField\Input\Checkbox
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_field/ToSConsentCheckbox.twig';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'form_field/input/checkbox/ToS_consent.css';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'form_field/input/checkbox/ToS_consent.js';

        return $list;
    }

    /**
     * @return bool
     */
    protected function isRequired()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Config::getInstance()->General->terms_conditions_confirm_type == 'Clickwrap';
    }

    /**
     * @return bool|mixed
     */
    public function getValue()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'I accept Terms and Conditions';
    }

    /**
     * @return array
     */
    protected function getDefaultLabelParams()
    {
        return [
            'URL' => $this->getTermsURL(),
        ];
    }

    /**
     * Get Terms and Conditions page URL
     *
     * @return string
     */
    public function getTermsURL()
    {
        return \XLite\Core\Config::getInstance()->General->terms_url;
    }
}
