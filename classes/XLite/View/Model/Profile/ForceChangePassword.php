<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\Profile;

use XCart\Extender\Mapping\ListChild;

/**
 * \XLite\View\Model\Profile\Main
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ForceChangePassword extends \XLite\View\Model\Profile\Main
{
    /**
     * Widget param names
     */
    public const PARAM_DATA_ERROR = 'data-error';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        if (\XLite::isAdminZone()) {
            $result[] = 'force_change_password';
        }

        return $result;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Update your password');
    }

    /**
     * Return text for header
     *
     * @return string
     */
    protected function getHeaderText()
    {
        return static::t('Admin has requested a change of password for your account. Please change the password before you proceed.');
    }

    /**
     * Display section header or not
     *
     * @param string $section Name of section to check
     *
     * @return boolean
     */
    protected function isShowSectionHeader($section)
    {
        return false;
    }

    /**
     * Defines directory where the templates and stylesheets are stored
     *
     * @return string
     */
    protected function getLoginDir()
    {
        return 'login';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'model/profile/force_change_password.less',
                $this->getLoginDir() . '/unauthorized/style.less',
                $this->getLoginDir() . '/login_form_fields.less'
            ]
        );
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                $this->getLoginDir() . '/script.js'
            ]
        );
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' change-password-form-container login-box';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DATA_ERROR => new \XLite\Model\WidgetParam\TypeString('Data error', ''),
        ];
    }

    /**
     * Prepare field arguments to create form field widget
     *
     * @param string $name Field name
     * @param array  $data Field data
     *
     * @return array
     */
    protected function getFieldSchemaArgs($name, array $data)
    {
        $data = parent::getFieldSchemaArgs($name, $data);

        $data[static::SCHEMA_ATTRIBUTES] += [
            static::PARAM_DATA_ERROR => static::t('Passwords do not match')
        ];

        return $data;
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        // Password is required
        unset($this->mainSchema['login']);
        unset($this->mainSchema['membership_id']);
        unset($this->mainSchema['pending_membership_id']);
        foreach (['password', 'password_conf'] as $field) {
            if (isset($this->mainSchema[$field])) {
                $this->mainSchema[$field][self::SCHEMA_CLASS] = 'XLite\View\FormField\Input\PasswordVisible';
                $this->mainSchema[$field][self::SCHEMA_REQUIRED] = true;
            }
        }

        return $this->getFieldsBySchema($this->mainSchema);
    }

    /**
     * Return text for the "Submit" button
     *
     * @return string
     */
    protected function getSubmitButtonLabel()
    {
        return 'Update';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        if ($this->isLogged()) {
            unset($result['delete_profile']);
        }

        return $result;
    }

    /**
     * Prepare posted data for mapping to the object
     *
     * @return array
     */
    protected function prepareDataForMapping()
    {
        $data = parent::prepareDataForMapping();
        if (!empty($data['password'])) {
            $data['forceChangePassword'] = false;
        }

        return $data;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Profile\ForceChangePassword';
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        $result = !\XLite\Core\Auth::comparePassword(
            \XLite\Core\Auth::getInstance()->getProfile()->getPassword(),
            $data['password']
        );

        if (!$result) {
            $formFields = $this->getFormFields();
            $this->addErrorMessage(
                'password',
                'The new password must not coincide with the current password for your account.',
                $formFields[self::SECTION_MAIN]
            );
        }

        parent::setModelProperties($data);
    }

    /**
     * Update profile
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        $result = parent::performActionUpdate();
        if ($this->isValid()) {
            \XLite\Core\Session::getInstance()->forceChangePassword = false;
        }

        return $result;
    }
}
