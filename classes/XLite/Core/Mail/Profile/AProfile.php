<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Profile;

use XLite\Core\Mailer;

abstract class AProfile extends \XLite\Core\Mail\AMail
{
    protected static function defineVariables()
    {
        return [
                'customer_email' => 'recipient@example.com',
                'first_name'     => static::t('Joe'),
            ] + parent::defineVariables();
    }

    public function __construct(\XLite\Model\Profile $profile)
    {
        parent::__construct();

        $this->setFrom(Mailer::getSiteAdministratorMail());
        if (static::getZone() === \XLite::ZONE_ADMIN) {
            $this->setTo(Mailer::getSiteAdministratorMails());
            $this->addReplyTo([
                'address' => $profile->getLogin(),
                'name'    => $profile->getName(false),
            ]);
        } else {
            $this->setTo(['email' => $profile->getLogin(), 'name' => $profile->getName(false)]);
            $this->setReplyTo(Mailer::getSiteAdministratorMails());
            $this->tryToSetLanguageCode($profile->getLanguage());
        }

        $this->appendData(['profile' => $profile]);
        $this->populateVariables([
            'customer_email' => $profile->getLogin(),
            'recipient_name' => $profile->getName(),
            'first_name'     => $profile->getName(true, true),
        ]);
    }
}
