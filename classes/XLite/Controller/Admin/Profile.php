<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Auth;
use XLite\Core\Request;
use XLite\Model\Role\Permission;

/**
 * Profile management controller
 */
class Profile extends \XLite\Controller\Admin\AAdmin
{
    use \XLite\Controller\Admin\ProfilePageTitleTrait;

    // 12h
    public const PASSWORD_RESET_KEY_EXP_TIME = 43200;

    /**
     * Controller parameters (to generate correct URL in getURL() method)
     *
     * @var array
     */
    protected $params = ['target', 'profile_id'];

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
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->isRegisterMode()) {
            return static::t('Create profile');
        }

        $title = $this->getTitleString(
            $this->getProfile()
        );

        return $title ?: static::t('Edit profile');
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        $profile = $this->getProfile();
        $auth = Auth::getInstance();
        $canEditCustomerProfiles = $auth->isPermissionAllowed('manage users');
        $canEditAdminProfiles = $auth->isPermissionAllowed('manage admins');

        $isAllowedForCurrentUser = $profile
            && (
                (
                    $canEditAdminProfiles
                    && $profile->isAdmin()
                    && (!$profile->isPermissionAllowed(Permission::ROOT_ACCESS) || $auth->hasRootAccess())
                )
                || (!$profile->isAdmin() && $canEditCustomerProfiles)
            );

        $isAllowedToCreateUsers = Request::getInstance()->mode === 'register'
            && (
                $canEditAdminProfiles
                || $canEditCustomerProfiles
            );

        return parent::checkACL()
            || $isAllowedForCurrentUser
            || $isAllowedToCreateUsers
            || $profile && $profile->getProfileId() == Auth::getInstance()->getProfile()->getProfileId();
    }

    /**
     * Check if current page is accessible
     *
     * @return bool
     */
    public function checkAccess()
    {
        return parent::checkAccess() && $this->isOrigProfile();
    }

    /**
     * Check controller visibility
     *
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getModelForm()->getModelObject();
    }

    /**
     * The "mode" parameter used to determine if we create new or modify existing profile
     *
     * @return bool
     */
    public function isRegisterMode()
    {
        return self::getRegisterMode() === \XLite\Core\Request::getInstance()->mode;
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->getModelForm()->getModelObject() ?: new \XLite\Model\Profile();
    }


    /**
     * Return true if profile is not related with any order (i.e. it's an original profile)
     *
     * @return bool
     */
    protected function isOrigProfile()
    {
        return !($this->getProfile()->getOrder());
    }

    /**
     * Class name for the \XLite\View\Model\ form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Profile\AdminMain';
    }

    /**
     * Define body classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);
        if ($this->isRegisterMode()) {
            $classes[] = 'register-mode';
        }

        return $classes;
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['finishOperateAs', 'recover_password']);
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        $profile = $this->getProfile();
        if ($profile) {
            \XLite\Core\Request::getInstance()->profile_type = $profile->isAdmin() ? 'A' : 'C';
        }
    }

    /**
     * Modify profile action
     */
    protected function doActionModify()
    {
        $this->getModelForm()->performAction('modify');
    }

    /**
     * actionPostprocessModify
     */
    protected function actionPostprocessModify()
    {
        if ($this->getModelForm()->isRegisterMode()) {
            // New profile is registered
            if ($this->isActionError()) {
                // Return back to register page
                $params = ['mode' => self::getRegisterMode()];
            } else {
                // Send notification
                \XLite\Core\Mailer::sendProfileCreated($this->getProfile());
                // Return to the created profile page
                $params = ['profile_id' => $this->getProfile()->getProfileId()];
            }
        } else {
            // Get profile ID from modified profile model
            $profileId = $this->getProfile()->getProfileId();
            // Return to the profile page
            $params = ['profile_id' => $profileId];
        }

        if (!empty($params)) {
            $this->setReturnURL($this->buildURL('profile', '', $params));
        }
    }

    /**
     * Delete profile action
     */
    protected function doActionDelete()
    {
        $this->getModelForm()->performAction('delete');

        // Send notification to the user
        \XLite\Core\Mailer::sendProfileDeleted($this->getProfile()->getLogin());

        $this->setReturnURL($this->buildURL('profile_list'));
    }

    /**
     * Register anonymous profile
     */
    protected function doActionRegisterAsNew()
    {
        $result = false;
        $profile = $this->getModelForm()->getModelObject();

        if (
            $profile
            && $profile->isPersistent()
            && $profile->getAnonymous()
            && !$profile->getOrder()
            && !\XLite\Core\Database::getRepo('XLite\Model\Profile')->findUserWithSameLogin($profile)
        ) {
            $profile->setAnonymous(false);
            $password = \XLite\Core\Database::getRepo('XLite\Model\Profile')->generatePassword();
            $profile->setPassword(Auth::encryptPassword($password));

            $result = $profile->update();
        }

        if ($result) {
            // Send notification to the user
            \XLite\Core\Mailer::sendRegisterAnonymousCustomer($profile, $password);

            \XLite\Core\TopMessage::addInfo('The profile has been registered. The password has been sent to the user\'s email address');
        }
    }

    /**
     * Merge anonymous profile with registered
     */
    protected function doActionMergeWithRegistered()
    {
        $result = false;
        $profile = $this->getModelForm()->getModelObject();

        if (
            $profile
            && $profile->isPersistent()
            && $profile->getAnonymous()
            && !$profile->getOrder()
        ) {
            $same = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findUserWithSameLogin($profile);
            if ($same && !$same->isAdmin()) {
                $same->mergeWithProfile($profile);
                $result = $same->update();
                if ($result) {
                    $profile->delete();
                }
            }
        }

        if ($result) {
            \XLite\Core\TopMessage::addInfo('The profiles have been merged');
            $this->setReturnURL(static::buildURL('profile', '', ['profile_id' => $same->getProfileId()]));
        }
    }

    /**
     * Operate as user
     */
    protected function doActionOperateAs()
    {
        $profile = $this->getModelForm()->getModelObject();

        if (
            $profile
            && !$profile->getAnonymous()
        ) {
            Auth::getInstance()->setOperatingAs($profile);

            \XLite\Core\TopMessage::addInfo(
                'You are operating as: user',
                ['user' => $profile->getLogin()]
            );
            $this->setReturnURL($this->getShopURL(''));
        }
    }


    /**
     * Login as admin
     */
    protected function doActionLoginAs()
    {
        $profile = $this->getModelForm()->getModelObject();

        if (
            $profile
            && !$profile->getAnonymous()
            && $profile->isAdmin()
            && !$profile->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
            && (Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS))
        ) {
            Auth::getInstance()->loginProfile($profile, false);

            \XLite\Core\TopMessage::addInfo(
                'You are logged in as: user',
                ['user' => $profile->getLogin()]
            );
            $this->setReturnURL(
                \XLite::getInstance()->getShopURL(
                    \XLite::getAdminScript()
                )
            );
        } else {
            if ($profile) {
                $this->setReturnURL($this->buildURL('profile', '', ['profile_id' => $profile->getProfileId()]));
            }
        }

        $this->setHardRedirect(true);
    }

    /**
     * doActionRecoverPassword
     */
    protected function doActionRecoverPassword()
    {
        /** @var \XLite\Model\Profile $profile */
        $profile = $this->getModelForm()->getModelObject();

        if ($this->requestRecoverPassword($profile)) {
            \XLite\Core\TopMessage::addInfo(
                'The confirmation URL link was mailed to email',
                ['email' => $profile->getLogin()]
            );
        }

        $this->setReturnURL($this->buildURL('profile', '', ['profile_id' => $profile->getProfileId()]));
    }

    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return bool
     */
    protected function requestRecoverPassword($profile)
    {
        $result = false;
        if (
            $profile
            && $profile->getProfileId() === Auth::getInstance()->getProfile()->getProfileId()
        ) {
            if (
                $profile->getPasswordResetKey() === ''
                || $profile->getPasswordResetKeyDate() === 0
                || \XLite\Core\Converter::time() > $profile->getPasswordResetKeyDate()
            ) {
                // Generate new 'password reset key'
                $profile->setPasswordResetKey($this->generatePasswordResetKey());
                $profile->setPasswordResetKeyDate(\XLite\Core\Converter::time() + static::PASSWORD_RESET_KEY_EXP_TIME);

                $profile->update();
            }

            \XLite\Core\Mailer::sendRecoverPasswordRequest($profile, $profile->getPasswordResetKey());

            $result = true;
        }

        return $result;
    }

    /**
     * Generates password reset key
     *
     * @return string
     */
    protected function generatePasswordResetKey()
    {
        $result = Auth::encryptPassword(microtime(), Auth::DEFAULT_HASH_ALGO);

        if (
            !empty($result)
            && strpos($result, Auth::DEFAULT_HASH_ALGO) === 0
        ) {
            $result = substr($result, 7);
        }

        return $result;
    }
}
