<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use QSL\AbandonedCartReminder\Core\Mail\Profile\AbandonmentEmail;

/**
 * @Extender\Mixin
 */
class Mailer extends \XLite\Core\Mailer
{
    /**
     * Send abandonment email.
     *
     * @param \XLite\Model\Profile $profile Customer profile
     * @param string               $subject E-mail subject
     * @param string               $body    E-mail body
     *
     * @return bool
     */
    public static function sendAbandonmentEmail($profile, $subject, $body)
    {
        $unsubscribed = Database::getRepo(\QSL\AbandonedCartReminder\Model\UnsubscribedUser::class)
            ->findOneByEmail(strtolower($profile->getLogin()));

        $result = false;

        if ($subject && $body && ! $unsubscribed) {
            $result = (new AbandonmentEmail($profile, $subject, $body))->send();
        }

        return $result;
    }
}
