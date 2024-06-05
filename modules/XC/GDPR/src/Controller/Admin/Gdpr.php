<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Marketplace;
use XC\GDPR\Core\Activity;

class Gdpr extends \XLite\Controller\Admin\AAdmin
{
    public function getTitle()
    {
        return static::t('GDPR activities');
    }

    protected function doNoAction()
    {
        $this->renewModulesIfRequired();
    }

    protected function renewModulesIfRequired()
    {
        if (!Marketplace::getInstance()->isGdprModulesListActual()) {
            Activity::updateModules();
            Database::getEM()->flush();
        }
    }
}
