<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Recover password dialog
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class RecoverPasswordAdmin extends \XLite\View\AView
{
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
                'recover_password'
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
                $this->getDir() . '/login_form_fields.less',
                $this->getDir() . '/password_recovery_admin/style.less'
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
                $this->getDir() . '/script.js'
            ]
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        $password_recovery_tpl_folder = '/password_recovery_admin';

        $mode            = strval(\XLite\Core\Request::getInstance()->mode);
        $templatesByMode = [
            'recoverMessage'   => $password_recovery_tpl_folder . '/recover_message.twig',
            'enterNewPassword' => $password_recovery_tpl_folder . '/enter_new_password.twig',
        ];
        return $this->getDir() . ( $templatesByMode[$mode] ?? $password_recovery_tpl_folder . '/recover_password.twig');
    }

    /**
     * @return string
     */
    protected function getRecoverTitle()
    {
        return \XLite\Core\Request::getInstance()->valid
            ? static::t('Resend email')
            : static::t('Forgot your password?');
    }

    protected function getTitle()
    {
        return $this->isRecoverMessage()
            ? static::t('Reset password')
            : $this->getRecoverTitle();
    }

    protected function isRecoverMessage()
    {
        return \XLite\Core\Request::getInstance()->mode === 'recoverMessage';
    }

    protected function getMessageFirstLine()
    {
        return \XLite\Core\Request::getInstance()->valid
            ? static::t('Please check your email before clicking Submit')
            : static::t('To recover your password, please type in the valid e-mail address you use as a login');
    }

    protected function getMessageSecondLine()
    {
        return \XLite\Core\Request::getInstance()->valid
            ? ''
            : static::t('The confirmation URL link will be emailed to you shortly');
    }

    /**
     * Defines directory where the templates and stylesheets are stored
     *
     * @return string
     */
    protected function getDir()
    {
        return 'login';
    }
}
