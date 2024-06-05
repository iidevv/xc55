<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Controller\Customer;

/**
 * Social accounts
 */
class SocialAccounts extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target'];

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && \XLite\Core\Auth::getInstance()->isLogged()
            && $this->checkProfile();
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
     * @inheritdoc
     */
    public function getTitle()
    {
        return static::t('Social accounts');
    }

    /**
     * @inheritdoc
     */
    public function isTitleVisible()
    {
        return \XLite\Core\Request::getInstance()->widget;
    }

    /**
     * @inheritdoc
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * @inheritdoc
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('My account'));
    }

    /**
     * Remove external profile
     */
    protected function doActionRemove()
    {
        /** @var \QSL\OAuth2Client\Model\ExternalProfile $profile */ #nolint
        $profile = \XLite\Core\Request::getInstance()->id
            ? \XLite\Core\Database::getRepo('QSL\OAuth2Client\Model\ExternalProfile')->find(\XLite\Core\Request::getInstance()->id)
            : null;

        if ($profile && $profile->getProfile()->getProfileId() == \XLite\Core\Auth::getInstance()->getProfile()->getProfileId()) {
            \XLite\Core\Database::getEM()->remove($profile);
            \XLite\Core\Database::getEM()->flush();
        }
    }
}
