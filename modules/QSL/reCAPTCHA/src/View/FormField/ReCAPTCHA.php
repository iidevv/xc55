<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\FormField;

use XLite\InjectLoggerTrait;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;

/**
 * Google reCaptcha v2.0 widget field.
 *
 * See:
 * - https://developers.google.com/recaptcha/intro
 */
class ReCAPTCHA extends \XLite\View\FormField\AFormField
{
    use InjectLoggerTrait;

    /**
     * Field type.
     */
    public const FIELD_TYPE_RECAPTCHA = 'recaptcha';

    /**
     * Name of the POST parameter that Google reCAPTCHA submits to the server for verification.
     */
    public const POST_PARAM_NAME = 'g-recaptcha-response';

    protected $grecaptchaFieldId;

    /**
     * Returns the field type.
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_CAPTCHA;
    }

    /**
     * Returns the name of the folder with widget-related files.
     *
     * @return string
     */
    protected function getDir()
    {
        return \QSL\reCAPTCHA\Main::isAPIv3()
            ? 'modules/QSL/reCAPTCHA/form_field/reCAPTCHA/v3'
            : 'modules/QSL/reCAPTCHA/form_field/reCAPTCHA';
    }

    /**
     * Returns the field template.
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'body.twig';
    }

    /**
     * getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $config = \XLite\Core\Config::getInstance()->QSL->reCAPTCHA;

        return array_merge(
            parent::getAttributes(),
            [
                'class'         => 'google-recaptcha-wrapper google-recaptcha-wrapper--empty',
                'data-sitekey'  => $this->getPublicKey(),
                'data-theme'    => $config->google_recaptcha_theme
                    ?: \QSL\reCAPTCHA\View\FormField\Select\Theme::getDefault(),
                'data-size'     => $config->google_recaptcha_size
                    ?: \QSL\reCAPTCHA\View\FormField\Select\Size::getDefault(),
                'data-tabindex' => 0,
                'data-language' => \XLite\Core\Session::getInstance()->getLanguage()->getCode(),
            ]
        );
    }

    protected function getInputAttributes()
    {
        $addAttributes = [
            'type'          => 'hidden',
            'name'          => static::POST_PARAM_NAME,
            'id'            => "grecaptcha_hidden_field_{$this->getFieldId()}",
            'data-language' => \XLite\Core\Session::getInstance()->getLanguage()->getCode(),
        ];

        return array_merge(
            parent::getAttributes(),
            $addAttributes
        );
    }

    public function getFieldId()
    {
        if (is_null($this->grecaptchaFieldId)) {
            $this->grecaptchaFieldId = str_replace('.', '', microtime(true));
        }

        return $this->grecaptchaFieldId;
    }

    /**
     * Get "action" param for recaptcha v3 API
     *
     * @see https://developers.google.com/recaptcha/docs/v3#actions
     *
     * @return string
     */
    protected function getActionAttribute()
    {
        return str_replace('_', '', \XLite::getController()->getTarget());
    }

    /**
     * Returns the public site key for Google reCAPTCHA.
     *
     * @return string
     */
    protected function getPublicKey()
    {
        return \XLite\Core\Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_public ?: '';
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    /*
        // We have to use getJSFiles() and getCSSFiles() instead of getCommonFiles()
        // because resources from getCommonFiles() aren't loaded dynamically when
        // displayed in an ajax popup.

        // TODO: switch back when BT#0047623 is resolved

        public function getCommonFiles()
        {
            $list = parent::getCommonFiles();
            $list[static::RESOURCE_CSS][] = $this->getDir() . '/reCAPTCHA.css';
            $list[static::RESOURCE_JS][] = $this->getDir() . '/reCAPTCHA.js';

            return $list;
        }
    */

    /**
     * Returns the list of required JS resources.
     *
     * @return array
     */
    public function getJSFiles()
    {
        if (\QSL\reCAPTCHA\Main::isAPIv3()) {
            return parent::getJSFiles();
        }

        return array_merge(
            parent::getJSFiles(),
            [
                $this->getDir() . '/reCAPTCHA.js',
            ]
        );
    }

    /**
     * Returns the list of required CSS resources.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                $this->getDir() . '/reCAPTCHA.css',
            ]
        );
    }

    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $valid = parent::checkFieldValidity();

        // Skip the verification when the form is displayed the first time
        if ($this->isFormSubmitted()) {
            $v = Validator::getInstance()->verify($this->getResponseValue());

            if (!$v->isSuccess()) {
                $valid = false;

                $fatal = $v->getFatalErrorMessages();
                if (!empty($fatal)) {
                    $this->getLogger('QSL-reCAPTCHA')->error('', $fatal);
                }

                $this->errorMessage = \QSL\reCAPTCHA\Main::isAPIv3()
                    ? $v->getChallengeMessage() ?: static::t('reCAPTCHA uknown error')
                    : static::t('Please confirm that you are not a robot');
            } elseif ($infoMessage = $v->getChallengeMessage()) {
                \XLite\Core\TopMessage::addInfo($infoMessage);
            }
        }

        return $valid;
    }

    /**
     * Returns the challenge response value added by the Google reCAPTCHA widget.
     *
     * @return string
     */
    protected function getResponseValue()
    {
        return \XLite\Core\Request::getInstance()->{self::POST_PARAM_NAME};
    }

    /**
     * Check if the form has been submitted.
     *
     * @return bool
     */
    protected function isFormSubmitted()
    {
        $controller        = \XLite::getController();
        $route             = $controller->getTarget() . '/' . $controller->getAction();
        $isSigninAnonymous = (bool) \XLite\Core\Request::getInstance()->is_signin_anonymous;

        if ($route === 'checkout/update_profile') {
            return $isSigninAnonymous;
        }

        return (bool) \XLite\Core\Request::getInstance()->{\XLite::FORM_ID}
            || ($route === 'contact_us/send')
            // || ($route === 'checkout/update_profile' && $isSigninAnonymous)
            || ($route === 'advanced_contact_us/send')
            || ($route === 'register_vendor/register')
            || ($route === 'recover_password/recover_password');
    }
}
