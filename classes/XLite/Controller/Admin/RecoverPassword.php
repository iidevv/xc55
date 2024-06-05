<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Auth;
use XLite\Core\TopMessage;

/**
 * Password recovery controller
 * TODO: full refactoring is needed
 */
class RecoverPassword extends \XLite\Controller\Admin\AAdmin
{
    // 12h
    public const PASSWORD_RESET_KEY_EXP_TIME = 43200;

    /**
     * params
     *
     * @var string
     */
    protected $params = ['target', 'mode', 'email', 'link_mailed'];

    /**
     * getAccessLevel
     *
     * @return integer
     */
    public function getAccessLevel()
    {
        return \XLite\Core\Auth::getInstance()->getCustomerAccessLevel();
    }

    /**
     * Check - is current place public or not
     *
     * @return boolean
     */
    protected function isPublicZone()
    {
        return \XLite\Core\Request::getInstance()->target == 'recover_password';
    }

    /**
     * doActionRecoverPassword
     *
     * @return void
     */
    protected function doActionRecoverPassword()
    {
        // show recover message if email is valid
        if ($this->requestRecoverPassword($this->get('email'))) {
            if (Auth::getInstance()->isLogged()) {
                $this->setReturnURL($this->buildURL('profile'));
                TopMessage::addInfo(
                    'The confirmation URL link was mailed to email',
                    [
                        'email' => $this->get('email'),
                    ]
                );
            } else {
                $this->setReturnURL(
                    $this->buildURL(
                        'recover_password',
                        '',
                        [
                            'mode'  => 'recoverMessage',
                            'email' => $this->get('email'),
                        ]
                    )
                );
            }
        } else {
            $this->setReturnURL($this->buildURL('recover_password', '', ['valid' => 0, 'email' => $this->get('email')]));
            \XLite\Core\TopMessage::addError('There is no user with specified email address');
        }
    }

    /**
     * @throws \Exception
     */
    protected function doActionSetNewPassword()
    {
        $result = '';
        if (
            $this->get('email')
            && $this->get('request_id')
            && ($result = $this->doPasswordRecovery($this->get('email'), $this->get('request_id'), $this->get('password'), $this->get('password_conf')))
            && $result === true
        ) {
            \XLite\Core\TopMessage::addInfo(
                'Password has been updated successfully'
            );

            \XLite\Core\Event::recoverPasswordDone(['email' => $this->get('email')]);
        }
        $this->setReturnURL($this->buildFullURL(is_string($result) ? $result : ''));
    }

    /**
     * requestRecoverPassword
     *
     * @param mixed $email Email
     *
     * @return boolean
     */
    protected function requestRecoverPassword($email)
    {
        $result = false;

        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($email);

        if (
            isset($profile)
            && $profile->isAdmin()
        ) {
            if (
                $profile->getPasswordResetKey() == ''
                || $profile->getPasswordResetKeyDate() == 0
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
     * @param string $email     Profile email
     * @param string $requestID Request ID
     * @param        $password
     * @param        $password_conf
     *
     * @return mixed
     * @throws \Exception
     */
    protected function doPasswordRecovery($email, $requestID, $password, $password_conf)
    {
        $result = false;

        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($email);

        if (!isset($profile) || !$profile->isAdmin()) {
            \XLite\Core\TopMessage::addError('There is no user with specified email address');
        } elseif (
            $profile->getPasswordResetKey() != $requestID
            || \XLite\Core\Converter::time() > $profile->getPasswordResetKeyDate()
        ) {
            \XLite\Core\TopMessage::addError('Your "Password reset key" has expired. Please enter the email address associated with your user account to get a new "Password reset key".');

            $profile->setPasswordResetKey('');
            $profile->setPasswordResetKeyDate(0);

            $profile->update();

            \XLite\Core\Auth::getInstance()->logoff();
            $this->setHardRedirect(true);
            $result = 'recover_password';
        } elseif (
            empty($password)
            || empty($password_conf)
            || $password != $password_conf
        ) {
            \XLite\Core\TopMessage::addError('Password and its confirmation do not match');
        } elseif (\XLite\Core\Auth::comparePassword($profile->getPassword(), $password)) {
            \XLite\Core\Event::getInstance()->trigger('changePassword.error', [
                'type' => 'PASSWORD_MATCHES_CURRENT',
                'errorMessage' => static::t('Your new password cannot match your current one.')
            ]);
        } else {
            $profile->setPassword(\XLite\Core\Auth::encryptPassword($password));
            $profile->setPasswordResetKey('');
            $profile->setPasswordResetKeyDate(0);

            $result = $profile->update();

            if ($result) {
                $this->setHardRedirect(true);

                if (!\XLite\Core\Auth::getInstance()->isLogged()) {
                    \XLite\Core\Auth::getInstance()->loginProfile($profile);
                }
            }
        }

        return $result;
    }

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return false;
    }

    /**
     * Generates password reset key
     *
     * @return string
     */
    protected function generatePasswordResetKey()
    {
        $result = \XLite\Core\Auth::encryptPassword(microtime(), \XLite\Core\Auth::DEFAULT_HASH_ALGO);

        if (
            !empty($result)
            && strpos($result, \XLite\Core\Auth::DEFAULT_HASH_ALGO) === 0
        ) {
            $result = substr($result, 7);
        }

        return $result;
    }

    /**
     * Redirect if user already logged
     */
    protected function doNoAction()
    {
        if (\XLite\Core\Auth::getInstance()->isLogged()) {
            \XLite\Core\TopMessage::addInfo(
                'You are logged in as: user',
                ['user' => \XLite\Core\Auth::getInstance()->getProfile()->getLogin()]
            );

            $this->redirect($this->buildURL());
        }
    }

    /**
     * Redirect if user already logged
     */
    protected function doActionConfirm()
    {
        $this->doNoAction();
    }
}
