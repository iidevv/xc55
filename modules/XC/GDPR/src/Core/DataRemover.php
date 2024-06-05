<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core;

use XLite\Core\Database;
use XLite\Model\Profile;

/**
 * DataRemover
 */
class DataRemover extends \XLite\Base\Singleton
{
    public function removeByProfile(Profile $profile)
    {
        $this->removeAnonymousProfile($profile);
    }

    protected function removeAnonymousProfile(Profile $profile)
    {
        $repo = Database::getRepo('XLite\Model\Profile');

        if ($anon = $repo->findOneAnonymousByProfile($profile)) {
            Database::getEM()->remove($anon);
        }
    }
}
