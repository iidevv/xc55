<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;

use XLite\Core\Mailer;

class UpgradeSafeMode extends \XLite\Core\Mail\AMail
{
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return 'upgrade_access_keys';
    }

    public function __construct()
    {
        parent::__construct();

        $this->setFrom(Mailer::getSiteAdministratorMail());
        $this->setTo(Mailer::getSiteAdministratorMails());
    }
}
