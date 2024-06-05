<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core\Activity;

use Includes\Utils\Module\Manager;
use XLite\Core\Database;
use XC\GDPR\Model\Activity as ActivityModel;

class Module extends Common
{
    protected static $gdprModules;

    /**
     * @param string $moduleId
     *
     * @return ActivityModel
     */
    public static function update($moduleId)
    {
        $module = static::isModuleSuitable($moduleId);
        if ($module) {
            $type = ActivityModel::TYPE_MODULE;

            if (!($activity = static::findByItemAndType($moduleId, $type))) {
                $activity = static::createByItemAndType($moduleId, $type);
                $activity->setDate($module['installedDate']);
                Database::getEM()->persist($activity);
            }

            $activity->setDetails(array_merge($activity->getDetails(), [
                'name'     => $module['moduleName'],
                'activity' => $module['description'],
            ]));

            return $activity;
        }

        return null;
    }

    /**
     * @param string $moduleId
     *
     * @return bool
     */
    protected static function isModuleSuitable($moduleId)
    {
        if (static::$gdprModules === null) {
            static::$gdprModules = \XLite\Core\Marketplace::getInstance()->retrieveGdprModules() ?: [];
        }

        $module = Manager::getRegistry()->getModule($moduleId);
        foreach (static::$gdprModules as $gdprModule) {
            if ($gdprModule['id'] === $module->id) {
                return $gdprModule;
            }
        }

        return null;
    }
}
