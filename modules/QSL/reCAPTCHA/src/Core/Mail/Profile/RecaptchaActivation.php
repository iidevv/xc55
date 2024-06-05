<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Core\Mail\Profile;

use XLite\Core\Converter;
use XLite\Core\Mail\Profile\AProfile;

class RecaptchaActivation extends AProfile
{
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public static function getDir()
    {
        return 'modules/QSL/reCAPTCHA/message';
    }

    protected static function defineVariables()
    {
        return parent::defineVariables() + [
                'activation_url' => Converter::buildFullURL('something_went_wrong'),
            ];
    }

    public function __construct(\XLite\Model\Profile $profile)
    {
        parent::__construct($profile);

        $activationUrl = Converter::buildFullURL(
            'profile',
            'recaptcha_activate',
            $this->getActivationUrlParams($profile)
        );

        $this->populateVariables(['activation_url' => $activationUrl]);
    }

    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return array
     */
    protected function getActivationUrlParams($profile)
    {
        return [
            'id'  => $profile->getProfileId(),
            'key' => $profile->getRecaptchaActivationKey(),
        ];
    }
}
