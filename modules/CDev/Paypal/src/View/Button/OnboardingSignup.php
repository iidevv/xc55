<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button;

/**
 * Sign up button
 */
class OnboardingSignup extends \CDev\Paypal\View\Button\SignUp
{
    /**
     * Defines the default location path
     *
     * @return string
     */
    protected function getDefaultLocation()
    {
        $api = \CDev\Paypal\Main::getRESTAPIInstance();

        return $api->isInContextSignUpAvailable()
            ? $this->getReferralPageURL()
            : parent::getDefaultLocation();
    }

    /**
     * Get URL of referral page
     *
     * @return string
     */
    public function getReferralPageURL()
    {
        $api = \CDev\Paypal\Main::getRESTAPIInstance();
        $controller = \XLite::getController();

        $returnUrl = $controller->getShopURL(
            $controller->buildURL('onboarding_wizard', 'update_credentials')
        );

        return $api->getSignUpUrl($returnUrl);
    }

    /**
     * Get button css class
     *
     * @return string
     */
    protected function getClass()
    {
        return 'btn regular-button';
    }
}
