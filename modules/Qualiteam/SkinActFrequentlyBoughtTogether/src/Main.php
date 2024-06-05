<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether;

use XCart\Domain\ModuleManagerDomain;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Is module enabled|disabled
     *
     * @param string $moduleId
     *
     * @return bool|null
     */
    public static function isModuleEnabled(string $moduleId)
    {
        $modules = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);

        return $modules->isEnabled($moduleId) ?? false;
    }
}