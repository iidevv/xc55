<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Logic\reCAPTCHA;

use QSL\reCAPTCHA\View\FormField\Select\FallbackAction;

/**
 * This class represents the result of a Google reCAPTCHA verification.
 *
 * @see https://developers.google.com/recaptcha/docs/verify
 */
class Result extends \XLite\Base\SuperClass
{
    /**
     * Google reCAPTCHA error codes.
     */
    public const ERROR_INVALID_JSON     = 'invalid-json-data';
    public const ERROR_NO_SECRET        = 'missing-input-secret';
    public const ERROR_INVALID_SECRET   = 'invalid-input-secret';
    public const ERROR_NO_RESPONSE      = 'missing-input-response';
    public const ERROR_INVALID_RESPONSE = 'invalid-input-response';
    public const ERROR_LOW_SCORE_VALUE  = 'low-score-value';

    public const CHALLENGE_CODE_LINK_SENT = 'link-sent';

    public const PARAM_THROTTLING_LAST_ATTEMPT = 'throttling_last_attempt';

    public const THROTTLING_SECONDS = 60;

    /*
     * Fields specific for Google reCAPTCHA.
     */
    protected $success = false;

    protected $challengeTs;

    protected $hostname;

    protected $errorCodes = [];

    protected $challengeCode = '';

    protected $challengeMessage = '';

    /**
     * Creates an instance from a JSON-enconded string returned by Google reCAPTCHA servers.
     *
     * @param string $json Google reCAPTCHA server response
     *
     * @return Result
     */
    public static function createFromJSON($json)
    {
        $r = json_decode($json);

        if (!is_null($r)) {
            $errorCodes = $r->{'error-codes'} ?? [];
            $hostname = $r->hostname ?? [];
            $ts = $r->challenge_ts ?? null;
            $score = $r->score ?? null;

            return new static($r->success, $errorCodes, $hostname, $ts, $score);
        } else {
            return new static(false, [self::ERROR_INVALID_JSON]);
        }
    }

    /**
     * Constructor.
     *
     * @return Result
     */
    public function __construct($success, $errorCodes = [], $host = null, $ts = null, $score = null)
    {
        $this->success     = $success;
        $this->challengeTs = $ts;
        $this->hostname    = $host;
        $this->errorCodes  = (array) $errorCodes;
        $this->score       = $score;

        if ($this->isLowScoreValue()) { // API v3 specific
            $this->success      = false;
            $this->errorCodes[] = static::ERROR_LOW_SCORE_VALUE;
        }

        if (!$this->success && \QSL\reCAPTCHA\Main::isAPIv3()) {
            $this->success = $this->isChallengePassed();
        }
    }

    public function isLowScoreValue()
    {
        if (
            \QSL\reCAPTCHA\Main::isAPIv3()
            && !is_null($this->score)
            && (float) $this->getMinimalScore() > (float) $this->score
        ) {
            return true;
        }

        return false;
    }

    protected function isChallengePassed()
    {
        $challenge = $this->challengeCode = $this->getChallengeCode();

        switch ($challenge) {
            case FallbackAction::ACTION_DENY_FORM:
                $this->challengeMessage = self::t('Sorry, there is suspicious activity registered in your network. You cannot proceed.');

                return false;

            case FallbackAction::ACTION_DO_NOTHING:
                $this->challengeMessage = "You seem like a bot to me, but I'm totally fine with it!";

                return true;

            case FallbackAction::ACTION_SEND_CONFIRMATION_LINK:
                $this->sendActivationLink();
                $this->challengeMessage = self::t('There was email with activation link sent to your account. Please click it to confirm your registration.');

                return true;

            case FallbackAction::ACTION_THROTTLE:
                return $this->checkThrottleChallenge();
        }
    }

    public function getChallengeCode()
    {
        $config = \XLite\Core\Config::getInstance()->QSL->reCAPTCHA;
        $key    = $this->getConfigPrefixByTarget() . "_fallback";

        return $config->$key ?? FallbackAction::ACTION_DO_NOTHING;
    }

    public function getMinimalScore()
    {
        $config = \XLite\Core\Config::getInstance()->QSL->reCAPTCHA;
        $key    = $this->getConfigPrefixByTarget() . "_min_score";

        return isset($config->$key) && !empty(trim($config->$key))
            ? (float) trim($config->$key)
            : (float) $config->google_recaptcha_min_score;
    }

    protected function getConfigPrefixByTarget()
    {
        switch ($target = \XLite::getController()->getTarget()) {
            case 'login':
            case 'recover':
                return "recaptcha_{$target}";

            case 'profile':
            case 'checkout':
                return 'recaptcha_register';

            case 'contact_us':
            case 'advanced_contact_us':
                return 'recaptcha_contact';

            case 'register_vendor':
                return 'recaptcha_vendor';

            case 'newsletter_subscriptions':
                return 'recaptcha_newsletter';

            default:
                return '';
        }
    }

    public function getChallengeMessage()
    {
        return $this->challengeMessage;
    }

    public function getChallengeCodeResult()
    {
        return $this->challengeCode;
    }

    protected function sendActivationLink()
    {
        \XLite::getController()->setRequiresActivation(true);
    }

    protected function checkThrottleChallenge()
    {
        $lastAttempt      = \XLite\Core\Session::getInstance()->{self::PARAM_THROTTLING_LAST_ATTEMPT} ?: 0;
        $trottlingSeconds = \XLite\Core\Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_throttling;
        $time             = time();
        $delta            = $time - $lastAttempt;

        if ($delta < $trottlingSeconds) {
            $tryAgainIn             = $trottlingSeconds - $delta;
            $this->challengeMessage = self::t(
                'Only one attempt per X seconds is allowed! Please try again in Y seconds.',
                ['seconds' => $trottlingSeconds, 'remaining' => $tryAgainIn]
            );

            return false;
        }

        \XLite\Core\Session::getInstance()->{self::PARAM_THROTTLING_LAST_ATTEMPT} = $time;

        return true;
    }

    /**
     * Checks if the user has been verified by Google reCAPTCHA successfully.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return (bool) $this->success;
    }

    /**
     * Returns error codes.
     *
     * @return array
     */
    public function getErrorCodes()
    {
        return (array) $this->errorCodes;
    }

    /**
     * Returns translated error messages.
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $result = [];

        $translations = $this->getErrorCodeTranslations();

        foreach ($this->getErrorCodes() as $code) {
            $result[$code] = $translations[$code] ?? $code;
        }

        return $result;
    }

    /**
     * Ignores CAPTCHA-related errors and returns only those errors which may
     * indicate some problems with the API itself.
     *
     * @return array
     */
    public function getFatalErrorMessages()
    {
        $result = [];

        foreach ($this->getErrorMessages() as $code => $message) {
            if (
                in_array($code, [
                self::ERROR_INVALID_JSON,
                self::ERROR_NO_SECRET,
                self::ERROR_INVALID_SECRET,
                ])
            ) {
                $result[$code] = $message;
            }
        }

        return $result;
    }

    /**
     * Returns error code messages.
     *
     * @return array
     */
    protected function getErrorCodeTranslations()
    {
        return [
            self::ERROR_NO_SECRET        => $this->t('The Google reCAPTCHA secret key is missing'),
            self::ERROR_INVALID_SECRET   => $this->t('The Google reCAPTCHA secret key is invalid or malformed'),
            self::ERROR_NO_RESPONSE      => $this->t('The Google reCAPTCHA response parameter is missing'),
            self::ERROR_INVALID_RESPONSE => $this->t('The Google reCAPTCHA response parameter is invalid or malformed'),
            self::ERROR_INVALID_JSON     => $this->t('The JSON data from Google reCAPTCHA vertification is invalid'),
            self::ERROR_LOW_SCORE_VALUE  => $this->t('Score is lower than defined threshold'),
        ];
    }
}
