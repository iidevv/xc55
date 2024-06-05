<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Password recovery controller
 */
class RecoverPassword extends \XLite\Controller\Customer\ACustomer
{
    // 12h
    public const PASSWORD_RESET_KEY_EXP_TIME = 43200;

    /**
     * params
     *
     * @var string
     */
    protected $params = ['target', 'email'];

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        $mode = \XLite\Core\Request::getInstance()->mode;
        return $mode === 'enterNewPassword' ? '' : static::t('Forgot password?');
    }

    /**
     * Add the base part of the location path
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('Help zone'));
    }

    /**
     * Common method to determine current location
     *
     * @return array
     */
    protected function getLocation()
    {
        $mode = \XLite\Core\Request::getInstance()->mode;
        return $mode === 'enterNewPassword' ? '' : $this->getTitle();
    }

    /**
     * doActionRecoverPassword
     *
     * @return void
     */
    protected function doActionRecoverPassword()
    {
        $email = $this->get('email');

        if ($this->requestRecoverPassword($email)) {
            $this->requestRecoverPasswordSuccess($email);
        } else {
            $this->requestRecoverPasswordFailed($email);
        }
    }

    /**
     * @param string $email
     */
    protected function requestRecoverPasswordSuccess($email)
    {
        \XLite\Core\TopMessage::addInfo(
            'The confirmation URL link was mailed to email',
            ['email' => $email]
        );

        if ($this->isAJAX()) {
            \XLite\Core\Event::recoverPasswordSent(['email' => $email]);
            $this->setSilenceClose();
        } else {
            $this->setReturnURL($this->buildURL());
        }
    }

    /**
     * @param string $email
     */
    protected function requestRecoverPasswordFailed($email)
    {
        $this->setReturnURL($this->buildURL('recover_password'));

        if (!$this->isAJAX()) {
            \XLite\Core\TopMessage::addError('There is no user with specified email address');
        }

        \XLite\Core\Event::invalidElement('email', static::t('There is no user with specified email address'));
    }

    /**
     * @return void
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
     * Is profile can be recovered
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return bool
     */
    protected function isRecoverAllowed(\XLite\Model\Profile $profile)
    {
        return !$profile->isAdmin();
    }

    /**
     * Sent Recover password mail
     *
     * @param string $email Email
     *
     * @return boolean
     */
    protected function requestRecoverPassword($email)
    {
        $result = false;

        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($email);

        if ($profile && $this->isRecoverAllowed($profile)) {
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

        if (!$profile || !$this->isRecoverAllowed($profile)) {
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
            \XLite\Core\TopMessage::addError('Personal info: The new password must not coincide with the current password for your account.');
        } else {
            $profile->setPassword(\XLite\Core\Auth::encryptPassword($password));
            $profile->setPasswordResetKey('');
            $profile->setPasswordResetKeyDate(0);

            $result = $profile->update();

            if ($result) {
                $this->setHardRedirect(true);

                if (!\XLite\Core\Auth::getInstance()->isLogged()) {
                    $successfullyLogged = \XLite\Core\Auth::getInstance()->loginProfile($profile);
                }

                if (!empty($successfullyLogged)) {
                    $profileCart = $this->getCart();

                    // We merge the logged in cart into the session cart
                    $profileCart->login($profile);
                    \XLite\Core\Database::getEM()->flush();

                    if ($profileCart->isPersistent()) {
                        $this->updateCart();
                        \XLite\Core\Event::getInstance()->exclude('updateCart');
                    }
                }
            }
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
