<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;

use XLite\Core\Converter;
use XLite\Core\Mailer;
use XLite\Model\AccessControlCell;
use XLite\Model\Profile;

class AccessLinkCustomer extends \XLite\Core\Mail\AMail
{
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public static function getDir()
    {
        return 'access_link';
    }

    protected static function defineVariables()
    {
        return [
                'first_name' => static::t('Joe'),
            ] + parent::defineVariables();
    }

    public function __construct(Profile $profile, AccessControlCell $acc)
    {
        parent::__construct();

        $this->setFrom(Mailer::getSiteAdministratorMail());
        $this->setTo(['email' => $profile->getLogin(), 'name' => $profile->getName(false)]);
        $this->setReplyTo(Mailer::getSiteAdministratorMails());
        $this->tryToSetLanguageCode($profile->getLanguage());

        $returnData = $acc->getReturnData();

        $link = Converter::buildPersistentAccessURL(
            $acc,
            $returnData['target'] ?? '',
            $returnData['action'] ?? '',
            $returnData['params'] ?? [],
            \XLite::getCustomerScript()
        );

        $this->appendData([
            'access_link' => $link,
            'profile' => $profile,
            'recipient_name' => $profile->getName(),
        ]);
        $this->populateVariables([
            'first_name' => $profile->getName(true, true),
        ]);
    }
}
