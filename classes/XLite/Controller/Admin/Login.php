<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * FIXME: must be completely refactored
 */
class Login extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return int
     */
    public function getAccessLevel()
    {
        return \XLite\Core\Auth::getInstance()->getCustomerAccessLevel();
    }

    public function init()
    {
        parent::init();

        if (empty(\XLite\Core\Request::getInstance()->login)) {
            \XLite\Core\Request::getInstance()->login = \XLite\Core\Auth::getInstance()->remindLogin();
        }
    }

    /**
     * Check - is current place public or not
     *
     * @return bool
     */
    protected function isPublicZone()
    {
        return true;
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        if (\XLite\Core\Auth::getInstance()->isAdmin()) {
            $this->setReturnURL($this->buildURL());
        }
    }

    protected function doActionLogin()
    {
        $profile = \XLite\Core\Auth::getInstance()->loginAdministrator(
            \XLite\Core\Request::getInstance()->login,
            \XLite\Core\Request::getInstance()->getNonFilteredData()['password'] ?? null
        );

        if (
            is_int($profile)
            && in_array($profile, [\XLite\Core\Auth::RESULT_ACCESS_DENIED, \XLite\Core\Auth::RESULT_PASSWORD_NOT_EQUAL, \XLite\Core\Auth::RESULT_LOGIN_IS_LOCKED])
        ) {
            $this->set('valid', false);

            if (in_array($profile, [\XLite\Core\Auth::RESULT_ACCESS_DENIED, \XLite\Core\Auth::RESULT_PASSWORD_NOT_EQUAL])) {
                \XLite\Core\TopMessage::addError('Invalid login or password');
            } elseif ($profile == \XLite\Core\Auth::RESULT_LOGIN_IS_LOCKED) {
                \XLite\Core\TopMessage::addError('Login is locked out');
            }

            $returnURL = $this->buildURL('login');
        } else {
            if (isset(\XLite\Core\Session::getInstance()->lastWorkingURL)) {
                $returnURL = \XLite\Core\Session::getInstance()->lastWorkingURL;
                unset(\XLite\Core\Session::getInstance()->lastWorkingURL);
            } else {
                $returnURL = $this->buildURL();
            }

            \Includes\Utils\Session::setAdminCookie();

            \XLite\Core\Database::getEM()->flush();
        }

        $this->setReturnURL($returnURL);
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), [
            'logoff',
            'verify'
        ]);
    }

    protected function doActionLogoff()
    {
        \Includes\Utils\Session::clearAdminCookie();

        \XLite\Core\Auth::getInstance()->logoff();

        \XLite\Model\Cart::getInstance()->logoff();
        \XLite\Model\Cart::getInstance()->updateOrder();

        \XLite\Core\Database::getEM()->flush();

        $this->setReturnURL($this->buildURL('login'));
    }
}
