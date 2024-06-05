<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;
use QSL\reCAPTCHA\View\FormField\Select\FallbackAction;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Controller\Customer\Profile
{
    /**
     * Postprocess register action
     *
     * @return void
     */
    protected function postprocessActionRegister()
    {
        parent::postprocessActionRegister();

        if ($this->getRequiresActivation() && !$this->isActionError()) {
            $this->setReturnURL($this->buildURL());
        }
    }

    protected function postprocessActionRegisterSuccess()
    {
        if ($this->getRequiresActivation()) {
            $this->sendRecaptchaActivationLink();
        }

        parent::postprocessActionRegisterSuccess();

        $validator = Validator::getInstance();
        if (
            $validator->isRequiredForRegistrationForm()
            && $validator->getChallengeCode() === FallbackAction::ACTION_SEND_CONFIRMATION_LINK
        ) {
            \XLite\Core\Auth::getInstance()->logoff();
        }
    }

    protected function sendRecaptchaActivationLink()
    {
        /** @var \XLite\Model\Profile $profile */
        $profile = $this->getModelForm()->getModelObject();
        $profile->setStatus(\XLite\Model\Profile::STATUS_DISABLED);
        $profile->setStatusComment(static::t('Disabled on registration because of reCAPTCHA low score'));
        $profile->setRecaptchaActivationKey();
        \XLite\Core\Database::getEM()->flush();

        \XLite\Core\Mailer::sendRecaptchaActivationEmail($profile);
    }

    protected function doActionRecaptchaActivate()
    {
        $profileId     = \XLite\Core\Request::getInstance()->id;
        $activationKey = \XLite\Core\Request::getInstance()->key;

        /**
         * @var \XLite\Model\Profile $profile
         */
        $profile = $profileId ? \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId) : null;

        // check for errors
        if (
            empty($activationKey)
            || !$profile
            || $activationKey !== $profile->getRecaptchaActivationKey()
        ) {
            \XLite\Core\TopMessage::addError(static::t('Error activating profile'));
            $this->setReturnURL($this->buildURL());

            return;
        }

        // 1. Enable profile
        $profile->setStatus(\XLite\Model\Profile::STATUS_ENABLED);
        $profile->setStatusComment(static::t('Enabled via activation link (reCAPTCHA)'));
        \XLite\Core\Database::getEM()->flush();

        // 2. Login user into the store
        \XLite\Core\Auth::getInstance()->loginProfile($profile);

        // 3. Register proper message and rediect
        \XLite\Core\TopMessage::addInfo(static::t('Your profile is activated!'));

        $this->setReturnURL($this->buildURL('address_book'));
    }

    /**
     * @inheritDoc
     */
    protected function checkAccess()
    {
        return $this->getAction() === 'recaptcha_activate' ? true : parent::checkAccess();
    }
}
