<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\FormField\Select\Select2;

class Countries extends \XLite\View\FormField\Select\Select2\Countries
{
    public function setValue($value)
    {
        $paramName = $this->getParam(static::PARAM_NAME);
        $origin_str = \XLite\Core\Config::getInstance()->XC->GDPR->$paramName;
        $origin_value = $this->getValuesArray($origin_str);

        if (is_string($value)) {
            $value = $this->getValuesArray($value);
        }

        if ($origin_value !== $value) {
            $this->updateCookieHash();
        }

        parent::setValue($value);
    }

    public function prepareRequestData($value)
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        return parent::prepareRequestData($value);
    }

    /**
     * Return empty if multiple selector clear
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/XC/GDPR/form_field/countries.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * @param string
     *
     * @return array
     */
    protected function getValuesArray($value)
    {
        return array_map('trim', explode(',', $value));
    }

    /**
     * @return void
     */
    protected function updateCookieHash()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'XC\GDPR',
            'name'     => 'cookie_hash',
            'value'    => md5(uniqid(\XLite\Core\Converter::time(), true)),
        ]);
    }
}
