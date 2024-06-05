<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Core\Mail\Profile;

use XLite\Core\Mail\Profile\AProfile;

/**
 * Decorated Mailer class.
 */
class AbandonmentEmail extends AProfile
{
    public function __construct(\XLite\Model\Profile $profile, $subject, $body)
    {
        parent::__construct($profile);

        $vars = [
            'subject'       => $subject,
            'body'          => $body,
         ];

        $this->populateVariables($vars);
    }

    protected static function defineVariables()
    {
        return parent::defineVariables() + [
                'subject' => static::t('Email subject'),
                'body' => static::t('Email body'),
            ];
    }

    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public static function getDir()
    {
        return 'modules/QSL/AbandonedCartReminder/abandonment_email';
    }
}
