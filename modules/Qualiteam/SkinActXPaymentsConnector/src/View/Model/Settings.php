<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Model;

use XLite\Core\Request;
use XLite\View\Button\AButton;
use XLite\View\Button\Submit;
use XLite\View\Model\ModuleSettings;

/**
 * Export payment methods form
 */
class Settings extends ModuleSettings
{
    /**
     * Get all schemas data
     *
     * @return array
     */
    protected function getAllSchemaCells()
    {
        $result = parent::getAllSchemaCells();

        if (!empty($result['xpc_private_key_password'])) {
            $result['xpc_private_key_password'][self::SCHEMA_TRUSTED] = true;
        }

        return $result;
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $formFields = parent::getFormFieldsForSectionDefault();

        $pageFields = \Qualiteam\SkinActXPaymentsConnector\Core\Settings::getInstance()
            ->getFieldsForPage(Request::getInstance()->page);

        foreach ($formFields as $field => $data) {
            // Remove fields from other pages
            if (!in_array($field, $pageFields)) {
                unset($formFields[$field]);
            }
        }

        return $formFields;
    }

    /**
     * Return list of form fields for certain section
     *
     * @param string $section Section name
     *
     * @return array
     */
    protected function getFormFieldsForSection($section)
    {
        // TODO: this function may not be required after final release, since it is WA for beta X-Cart core

        if (ucfirst($section) == 'Default') {
            $result = $this->getFormFieldsForSectionDefault();
        } else {
            $result = array();
        }

        return $result;
    }

    /**
     * Get editable options
     *
     * @return array
     */
    protected function getEditableOptions()
    {
        $options = parent::getEditableOptions();

        $pageOptions = \Qualiteam\SkinActXPaymentsConnector\Core\Settings::getInstance()
            ->getFieldsForPage(Request::getInstance()->page);

        foreach ($options as $key => $option) {
            // Remove options from other pages
            if (!in_array($option->name, $pageOptions)) {
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['addons-list'] = new \XLite\View\Button\SimpleLink(
            [
                AButton::PARAM_LABEL => static::t('Back to modules'),
                AButton::PARAM_STYLE => 'action addons-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => \XLite::getInstance()->getShopURL('service.php#/installed-addons', true, ['moduleId' => 'Qualiteam-SkinActXPaymentsConnector']),
            ]
        );

        $page = Request::getInstance()->page;
        if (\Qualiteam\SkinActXPaymentsConnector\Core\Settings::PAGE_CONNECTION == $page) {
            $result['submit'] = new Submit(
                array(
                    AButton::PARAM_LABEL    => 'Submit and test module',
                    AButton::PARAM_BTN_TYPE => 'regular-main-button',
                    AButton::PARAM_STYLE    => 'action',
                )
            );

        } elseif (\Qualiteam\SkinActXPaymentsConnector\Core\Settings::PAGE_PAYMENT_METHODS == $page) {
            $result = array();
        }

        return $result;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return \Qualiteam\SkinActXPaymentsConnector\View\Form\Settings::class;
    }

    /**
     * Flag if the panel widget for buttons is used
     *
     * @return boolean
     */
    protected function useButtonPanel()
    {
        return \Qualiteam\SkinActXPaymentsConnector\Core\Settings::PAGE_PAYMENT_METHODS != Request::getInstance()->page;
    }
}
