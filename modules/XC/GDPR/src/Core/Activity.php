<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core;

use Includes\Utils\Module\Manager;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XC\GDPR\Core\Activity\Admin;
use XC\GDPR\Core\Activity\Module;
use XC\GDPR\Core\Activity\Payment;
use XC\GDPR\Core\Activity\Shipping;

/**
 * Activity
 */
class Activity
{
    /**
     * Create & update all activities
     */
    public static function updateAllActivities()
    {
        static::updateModules();
        static::updateAdmins();
        static::updatePayments();
        static::updateShippingMethods();
    }

    /**
     * Create & update activities for admins
     */
    public static function updateAdmins()
    {
        $cnd = new CommonCell([
            \XLite\Model\Repo\Profile::SEARCH_PERMISSIONS => ['manage users', 'root access'],
            \XLite\Model\Repo\Profile::SEARCH_ONLY_REAL   => true,
        ]);

        $admins = Database::getRepo('XLite\Model\Profile')->search($cnd);

        foreach ($admins as $admin) {
            Admin::update($admin);
        }
    }

    /**
     * Create & update activities for modules
     */
    public static function updateModules()
    {
        foreach (Manager::getRegistry()->getEnabledModuleIds() as $moduleId) {
            Module::update($moduleId);
        }
    }

    /**
     * Create & update activities for payment methods
     */
    public static function updatePayments()
    {
        $methods = Database::getRepo('XLite\Model\Payment\Method')->findAllActive();

        foreach ($methods as $method) {
            Payment::update($method);
        }
    }

    /**
     * Create & update activities for payment methods
     */
    public static function updateShippingMethods()
    {
        $methods = Database::getRepo('XLite\Model\Shipping\Method')->findAll();

        foreach ($methods as $method) {
            /* @var \XLite\Model\Shipping\Method $method */
            Shipping::update($method);
        }
    }
}
