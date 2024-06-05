<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Module\XC\MailChimp\Core\Mail\Profile;

use XCart\Extender\Mapping\Extender;
use XC\MailChimp\Core;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\MailChimp")
 */
class RecaptchaActivation extends \QSL\reCAPTCHA\Core\Mail\Profile\RecaptchaActivation
{
    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return array
     */
    protected function getActivationUrlParams($profile)
    {
        $params = parent::getActivationUrlParams($profile);

        if ($subscribeToAll = \XLite\Core\Request::getInstance()->{Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME}) {
            $params[Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME] = $subscribeToAll;
            unset(\XLite\Core\Request::getInstance()->{Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME});
        }

        return $params;
    }
}
