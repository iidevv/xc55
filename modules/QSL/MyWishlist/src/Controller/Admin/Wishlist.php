<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Controller\Admin;

use XLite\Controller\Admin\ProfilePageTitleTrait;
use XLite\Core\Auth;

/**
 * Wishlist products page controller
 */
class Wishlist extends \XLite\Controller\Admin\ACL\Catalog
{
    use ProfilePageTitleTrait;

    /**
     * Wishlist model cache
     *
     * @var mixed
     */
    protected $wishlist = null;

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        $profile = $this->getProfileModel();
        $isAdmin = $profile && $profile->isAdmin();
        $auth = Auth::getInstance();

        return parent::checkACL()
            || $auth->isPermissionAllowed('[vendor] manage catalog')
            || ($isAdmin && $auth->isPermissionAllowed('manage admins'))
            || ($profile && !$isAdmin && $auth->isPermissionAllowed('manage users'));
    }

    /**
     * Wishlist defined from the request
     *
     * @return \QSL\MyWishlist\Model\Wishlist
     */
    public function getWishlist()
    {
        if (is_null($this->wishlist)) {
            $this->wishlist = \QSL\MyWishlist\Core\Wishlist::getInstance()
                ->getWishlist($this->getWishlistId(), $this->getProfileModel());
        }

        return $this->wishlist;
    }

    /**
     * Wishlist id is got from the request
     *
     * @return string
     */
    public function getWishlistId()
    {
        return \XLite\Core\Request::getInstance()->wishlist_id;
    }

    /**
     * Profile model
     *
     * @return \XLite\Model\Profile
     */
    public function getProfileModel()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($this->getProfileId());
    }

    /**
     * Profile identificator
     * Can be from the request or from the auth profile
     *
     * @return string
     */
    public function getProfileId()
    {
        return \XLite\Core\Request::getInstance()->profile_id ?: \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        if (\XLite\Core\Request::getInstance()->wishlist_id) {
            $name = $this->getWishlist()->getWishlistName();

            return static::t('Wishlist') . ($name ? (' "' . $name . '"') : '');
        } else {
            return $this->getTitleString(
                $this->getProfileModel()
            );
        }
    }

    /**
     * Common method to determine current location
     *
     * @return array
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        $profile = $this->getProfileModel();
        if ($profile) {
            \XLite\Core\Request::getInstance()->profile_type = $profile->isAdmin() ? 'A' : 'C';
        }
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
        $classes   = parent::defineBodyClasses($classes);
        $classes[] = 'profile-wishlist';

        return $classes;
    }
}
