<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Profile;

use XLite\Core\Converter;

class RecoverPasswordCustomer extends AProfile
{
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public static function getDir()
    {
        return 'recover_password_request';
    }

    protected static function defineVariables()
    {
        return parent::defineVariables() + [
                'recover_url' => \XLite::getInstance()->getShopURL(),
            ];
    }

    public function __construct(\XLite\Model\Profile $profile, $resetKey)
    {
        parent::__construct($profile);

        $url = \XLite::getInstance()->getShopURL(
            Converter::buildURL(
                'recover_password',
                'confirm',
                [
                    'email'      => $profile->getLogin(),
                    'mode'       => 'enterNewPassword',
                    'request_id' => $resetKey,
                ],
                \XLite::getCustomerScript()
            )
        );

        $this->populateVariables([
            'recover_url' => $url,
        ]);

        $this->appendData([
            'url' => $url,
        ]);
        $this->setFrom(\XLite\Core\Mailer::getUsersDepartmentMail());
        $this->setReplyTo(\XLite\Core\Mailer::getUsersDepartmentMails());
    }
}
