<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature;

use Includes\Utils\Module\Manager;
use XCart\Container;
use XCart\Domain\ModuleManagerDomain;
use XLite\Module\AModule;

/**
 * Class main
 */
class Main extends AModule
{
    /**
     * Get module path
     *
     * @return string
     */
    public static function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActColorSwatchesFeature';
    }

    /**
     * Is module enabled
     *
     * @return bool
     */
    public static function isModuleColorSwatchesEnabled(): bool
    {
        return Container::getContainer()->get(ModuleManagerDomain::class)->isEnabled('QSL-ColorSwatches');
    }
}
