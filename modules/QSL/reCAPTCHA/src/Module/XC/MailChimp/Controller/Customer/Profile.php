<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Module\XC\MailChimp\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XC\MailChimp\Core;

/**
 * User profile page controller
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\MailChimp")
 */
class Profile extends \XLite\Controller\Customer\Profile
{
    protected function actionPostprocessRecaptchaActivate()
    {
        if (\XC\MailChimp\Main::isMailChimpConfigured()) {
            $subscribeToAll = \XLite\Core\Request::getInstance()->{Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME};

            if ($subscribeToAll) {
                $profileId = \XLite\Core\Request::getInstance()->id;

                /**
                 * @var \XLite\Model\Profile $profile
                 */
                $profile = $profileId
                    ? \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId)
                    : null;

                if ($profile) {
                    try {
                        Core\MailChimp::processSubscriptionAll($profile);
                    } catch (Core\MailChimpException $e) {
                        \XLite\Core\TopMessage::addError(Core\MailChimp::getMessageTextFromError($e));
                    }
                }

                \XLite\Core\Session::getInstance()->{Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME} = null;
            }
        }
    }
}
