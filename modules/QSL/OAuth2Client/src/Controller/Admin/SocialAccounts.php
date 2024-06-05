<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Controller\Admin;

/**
 * Social accounts controller
 */
class SocialAccounts extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array()
     */
    protected $params = ['target', 'profile_id'];

    /**
     * @inheritdoc
     */
    public function checkACL()
    {
        $profile = $this->getProfile();

        $allowedForCurrentUser = \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage users');
        if ($profile && $profile->isAdmin() && !\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage admins')) {
            $allowedForCurrentUser = false;
        }

        return parent::checkACL()
            || $allowedForCurrentUser
            || $profile && $profile->getProfileId() == \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
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
    public function checkAccess()
    {
        return parent::checkAccess() && $this->isOrigProfile();
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getProfile();
    }

    /**
     * @inheritdoc
     */
    protected function isOrigProfile()
    {
        return !($this->getProfile() && $this->getProfile()->getOrder());
    }

    /**
     * @inheritdoc
     */
    public function getProfile()
    {
        return \XLite\Core\Request::getInstance()->profile_id
            ? \XLite\Core\Database::getRepo('XLite\Model\Profile')->find(\XLite\Core\Request::getInstance()->profile_id)
            : \XLite\Core\Auth::getInstance()->getProfile();
    }

    /**
     * Update list
     */
    protected function doActionUpdate()
    {
        $list = new \QSL\OAuth2Client\View\ItemsList\Model\ExternalProfile();
        $list->processQuick();
    }
}
