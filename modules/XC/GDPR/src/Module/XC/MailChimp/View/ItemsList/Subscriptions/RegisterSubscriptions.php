<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Module\XC\MailChimp\View\ItemsList\Subscriptions;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\MailChimp")
 */
class RegisterSubscriptions extends \XC\MailChimp\View\ItemsList\Subscriptions\RegisterSubscriptions
{
    /**
     * @param \XC\MailChimp\Model\MailChimpList $list    List
     * @param \XLite\Model\Profile|null                      $profile Profile
     *
     * @return boolean
     */
    protected function checkIfSubscribed(\XC\MailChimp\Model\MailChimpList $list, $profile)
    {
        if ($profile) {
            return \XLite\Core\Database::getRepo('XC\MailChimp\Model\MailChimpList')
                ->isProfileSubscribed($list, $profile);
        }

        return !\XLite\Core\Auth::getInstance()->isUserFromGdprCountry() && $list->getSubscribeByDefault();
    }
}
