<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Controller\Customer\Profile
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->isRegisterMode()) {
            return static::t('New account');
        } elseif (\XLite\Core\Request::getInstance()->mode == 'delete') {
            return static::t('Delete account');
        } else {
            return static::t('My account');
        }
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return true;
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        array_pop($this->locationPath);
    }

    /**
     * Get register success URL arguments
     *
     * @return array
     */
    protected function getActionRegisterSuccessURL()
    {
        if (preg_match('/target=checkout(&|\Z)/', \XLite\Core\Request::getInstance()->returnURL)) {
            return ['checkout'];
        }

        return parent::getActionRegisterSuccessURL();
    }
}
