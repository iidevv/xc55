<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Validators\Payments;

use XLite\Core\Session;
use XLite\Model\Profile;

class Validator
{
    /**
     * @param \XLite\Model\Profile $profile
     */
    public function __construct(
        private Profile $profile
    )
    {
    }

    public function hasProfile(): bool
    {
        return (bool) $this->profile;
    }

    public function hasBillingAddress(): bool
    {
        return $this->hasProfile()
            && $this->profile->getBillingAddress();
    }

    public function hasCountry(): bool
    {
        return $this->hasBillingAddress()
            && $this->profile->getBillingAddress()->getCountry();
    }

    public static function hasValidKlarnaSession(): bool
    {
        return self::hasSession()
            && self::isCorrectedSession()
            && self::isSessionActual();
    }

    protected static function hasSession(): bool
    {
        return (bool) Session::getInstance()->klarna_session;
    }

    protected static function isCorrectedSession(): bool
    {
        return isset(Session::getInstance()->klarna_session['client_token']);
    }

    protected static function isSessionActual(): bool
    {
        $currentDate = new \DateTime();
        $expiringAt = Session::getInstance()->klarna_session['expiring_at'];
        return $currentDate < $expiringAt;
    }
}