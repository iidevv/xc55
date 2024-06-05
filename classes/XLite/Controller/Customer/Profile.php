<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * User profile page controller
 */
class Profile extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Types of model form
     */
    public const SECTIONS_MAIN      = 'main';
    public const SECTIONS_ADDRESSES = 'addresses';
    public const SECTIONS_ALL       = 'all';

    /**
     * Return value for the "register" mode param
     *
     * @return string
     */
    public static function getRegisterMode()
    {
        return 'register';
    }

    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        if (!$this->isLogged() && !$this->isRegisterMode()) {
            $this->setReturnURL($this->buildURL('login'));
        } elseif ($this->isLogged() && $this->isRegisterMode()) {
            $this->setReturnURL(
                call_user_func_array([$this, 'buildURL'], $this->getActionRegisterSuccessURL())
            );
        }

        parent::handleRequest();
    }

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return true;
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Returns title of the page
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->isRegisterMode()) {
            $title = static::t('New account');
        } elseif (\XLite\Core\Request::getInstance()->mode == 'delete') {
            $title = static::t('Delete account');
        } else {
            $title = static::t('Account details');
        }

        return $title;
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return \XLite\Core\Request::getInstance()->mode == 'delete';
    }

    /**
     * The "mode" parameter used to determine if we create new or modify existing profile
     *
     * @return boolean
     */
    public function isRegisterMode()
    {
        return self::getRegisterMode() === \XLite\Core\Request::getInstance()->mode
            || !$this->getModelForm()->getModelObject()->isPersistent();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return parent::checkAccess() && $this->checkProfile();
    }

    /**
     * Define current location for breadcrumbs
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        if (!$this->isRegisterMode()) {
            $this->addLocationNode(static::t('My account'));
        }
    }

    /**
     * Return class name of the register form
     *
     * @return string|void
     */
    protected function getModelFormClass()
    {
        return '\XLite\View\Model\Profile\Main';
    }

    /**
     * Check if profile is not exists
     *
     * @return boolean
     */
    protected function doActionValidate()
    {
        return $this->getModelForm()->performAction('validateInput');
    }

    /**
     * doActionRegister
     *
     * @return boolean
     */
    protected function doActionRegister()
    {
        if (!\XLite\Core\Auth::getInstance()->isLogged()) {
            $result = $this->getModelForm()->performAction('create');
            $this->postprocessActionRegister();
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Postprocess register action
     *
     * @return void
     */
    protected function postprocessActionRegister()
    {
        if ($this->isActionError()) {
            $this->postprocessActionRegisterFail();
            $this->setReturnURL(
                call_user_func_array([$this, 'buildURL'], $this->getActionRegisterFailURL())
            );
        } else {
            $this->postprocessActionRegisterSuccess();

            $this->setReturnURL(
                call_user_func_array([$this, 'buildURL'], $this->getActionRegisterSuccessURL())
            );
        }
    }

    /**
     * Postprocess register action (fail)
     *
     * @return array
     */
    protected function postprocessActionRegisterFail()
    {
    }

    /**
     * Get register fail URL arguments
     *
     * @return array
     */
    protected function getActionRegisterFailURL()
    {
        return [
            'profile',
            '',
            ['mode' => static::getRegisterMode()]
        ];
    }

    /**
     * Postprocess register action (success)
     *
     * @return array
     */
    protected function postprocessActionRegisterSuccess()
    {
        // Send notification
        \XLite\Core\Mailer::sendProfileCreated($this->getModelForm()->getModelObject());

        $this->getCart()->login($this->getModelForm()->getModelObject());

        // Log in user with created profile
        \XLite\Core\Auth::getInstance()->loginProfile($this->getModelForm()->getModelObject());

        $this->setHardRedirect();
    }

    /**
     * Get register success URL arguments
     *
     * @return array
     */
    protected function getActionRegisterSuccessURL()
    {
        return [
            'address_book',
            '',
            ['profile_id' => $this->getModelForm()->getProfileId(false)],
        ];
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');
    }

    /**
     * doActionModify
     *
     * @return void
     */
    protected function doActionModify()
    {
        if ($this->isRegisterMode()) {
            $this->doActionRegister();
        } else {
            $this->doActionUpdate();
        }
    }

    /**
     * doActionDelete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        if (\XLite\Core\Auth::getInstance()->isAdmin()) {
            \XLite\Core\TopMessage::addWarning(
                static::t('Administrator account cannot be deleted via customer interface.')
            );

            $result = false;
        } else {
            $userLogin = $this->getModelForm()->getModelObject()->getLogin();

            $result = $this->getModelForm()->performAction('delete');

            if ($result) {
                // Send notification to the users department
                \XLite\Core\Mailer::sendProfileDeleted($userLogin);
            }

            $this->setHardRedirect();
            $this->setReturnURL($this->buildURL());
        }
    }
}
