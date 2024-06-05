<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Auth;

class AddressBook extends \XLite\Controller\Admin\AAdmin
{
    use \XLite\Controller\Admin\ProfilePageTitleTrait;

    /**
     * @var \XLite\Model\Address
     */
    protected $address;

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        $profile = $this->getProfile();
        $auth = Auth::getInstance();

        $allowedForCurrentUser = false;
        if ($profile) {
            $allowedForCurrentUser = (
                ($profile->isAdmin() && $auth->isPermissionAllowed('manage admins'))
                || (!$profile->isAdmin() && $auth->isPermissionAllowed('manage users'))
            );
        }

        return parent::checkACL()
            || $allowedForCurrentUser
            || ($profile && $profile->getProfileId() == Auth::getInstance()->getProfile()->getProfileId());
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if (\XLite\Core\Request::getInstance()->widget) {
            return static::t('Address details');
        }

        $title = $this->getTitleString(
            $this->getProfile()
        );

        return $title ?: static::t('Edit profile');
    }

    /**
     * @return \XLite\Model\Address
     */
    public function getAddress()
    {
        if ($this->address === null) {
            $this->address = $this->getModelForm()->getModelObject();
        }

        return $this->address;
    }

    /**
     * Get addresses array for working profile
     *
     * @return array
     */
    public function getAddresses()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Address')
            ->findBy(
                [
                    'profile' => $this->getProfile()->getProfileId(),
                ]
            );
    }

    /**
     * @return string
     */
    public function getReturnURL()
    {
        if (\XLite\Core\Request::getInstance()->action) {
            $profileId = \XLite\Core\Request::getInstance()->profile_id;

            if (!isset($profileId)) {
                $profileId = $this->getAddress()->getProfile()->getProfileId();

                if (Auth::getInstance()->getProfile()->getProfileId() === $profileId) {
                    unset($profileId);
                }
            }

            $params = isset($profileId) ? ['profile_id' => $profileId] : [];

            $url = $this->buildURL('address_book', '', $params);
        } else {
            $url = parent::getReturnURL();
        }

        return $url;
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
        return parent::isVisible() && $this->getProfile();
    }

    /**
     * Return true if profile is not related with any order (i.e. it's an original profile)
     *
     * @return bool
     */
    protected function isOrigProfile()
    {
        return !($this->getProfile() && $this->getProfile()->getOrder());
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return $this->getModelForm()->getModelObject()->getProfile();
    }

    /**
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Address\Address';
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        $profile = $this->getProfile();
        if ($profile) {
            \XLite\Core\Request::getInstance()->profile_type = $profile->isAdmin() ? 'A' : 'C';
        }
    }

    protected function doActionSave()
    {
        if ($this->getModelForm()->performAction('update')) {
            $this->setHardRedirect();
        }
    }

    protected function doActionDelete()
    {
        $address = $this->getAddress();

        if (isset($address)) {
            if ($address->getIsBilling() || $address->getIsShipping()) {
                $profile = $address->getProfile();

                if ($profile) {
                    foreach ($profile->getAddresses() as $profileAddress) {
                        if ($address->getAddressId() != $profileAddress->getAddressId()) {
                            $profileAddress->setIsBilling($profileAddress->getIsBilling() || $address->getIsBilling());
                            $profileAddress->setIsShipping($profileAddress->getIsShipping() || $address->getIsShipping());

                            break;
                        }
                    }
                }
            }

            $address->delete();

            \XLite\Core\TopMessage::addInfo(
                static::t('Address has been deleted')
            );
        }
    }

    protected function doActionCancelDelete()
    {
        // Do nothing, action is needed just for redirection back
    }
}
