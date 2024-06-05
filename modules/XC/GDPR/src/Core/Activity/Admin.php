<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core\Activity;

use XLite\Core\Database;
use XLite\Model\Profile;
use XC\GDPR\Model\Activity as ActivityModel;

class Admin extends Common
{
    /**
     * @param Profile $profile
     *
     * @return ActivityModel
     */
    public static function update(Profile $profile)
    {
        if (static::isAdminSuitable($profile)) {
            $item = static::getItemByProfile($profile);
            $type = ActivityModel::TYPE_ADMIN;

            if (!($activity = static::findByItemAndType($item, $type))) {
                $activity = static::createByItemAndType($item, $type);
                $activity->setDate($profile->getAdded());
                Database::getEM()->persist($activity);
            }

            $activity->setDetails(array_merge($activity->getDetails(), [
                'id'    => $profile->getProfileId(),
                'login' => $profile->getLogin(),
            ]));

            return $activity;
        }

        return null;
    }

    /**
     * @param Profile $admin
     *
     * @return bool
     */
    protected static function isAdminSuitable(Profile $admin)
    {
        return $admin->isEnabled() && $admin->isPermissionAllowed('manage users');
    }

    /**
     * @param Profile $profile
     *
     * @return string
     */
    protected static function getItemByProfile(Profile $profile)
    {
        return $profile->getLogin();
    }
}
