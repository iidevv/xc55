<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Login page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Login extends \XLite\View\AView
{
    /**
     * Time left to unlock
     *
     * @var integer
     */
    protected $timeLeftToUnlock;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'login',
            ]
        );
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                $this->getDir() . '/script.js',
                'form_field/js/password_visible.js',
            ]
        );
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                $this->getDir() . '/unauthorized/style.less',
                $this->getDir() . '/style.less',
                $this->getDir() . '/login_form_fields.less',
                'form_field/css/password_visible.less',
            ]
        );
    }

    /**
     * Check - login is locked or not
     *
     * @return integer
     */
    protected function isLocked()
    {
        return 0 < $this->getTimeLeftToUnlock();
    }

    /**
     * Return time left to unlock
     *
     * @return integer
     */
    protected function getTimeLeftToUnlock()
    {
        if (!isset($this->timeLeftToUnlock)) {
            $this->timeLeftToUnlock = \XLite\Core\Session::getInstance()->dateOfLockLogin
                ? \XLite\Core\Session::getInstance()->dateOfLockLogin + \XLite\Core\Auth::TIME_OF_LOCK_LOGIN - \XLite\Core\Converter::time()
                : 0;
        }

        return $this->timeLeftToUnlock;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'login';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Return box title
     *
     * @return string
     */
    protected function getLoginBoxTitle()
    {
        return static::t('Administration Zone');
    }
}
