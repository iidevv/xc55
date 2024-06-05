<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField;

use Includes\Utils\URLManager;

class APIWarningField extends \XLite\View\FormField\AFormField
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_LABEL;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/api_warning_field.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_field/api_warning_field.twig';
    }

    /**
     * Return 'Getting started' url for api
     *
     * @return string
     */
    public function getExtendApiUrl()
    {
        return \XLite::getXCartURL('https://developer.x-cart.com/api');
    }

    /**
     * Api url for current shop
     *
     * @return string
     */
    public function getInnerApiUrl()
    {
        return URLManager::getShopURL('api');
    }
}
