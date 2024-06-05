<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Traits;

use Qualiteam\SkinActAftership\View\Admin\ShipstationCodeMapping as CodeMapping;

/**
 * Trait aftership
 */
trait AftershipTrait
{
    /**
     * Get main config name
     *
     * @return string
     */
    protected static function getMainConfigName(): string
    {
        return 'aftership_settings';
    }

    /**
     * Get module path
     *
     * @return string
     */
    protected function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActAftership';
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getAftershipPrefix(): string
    {
        return 'aftership';
    }

    protected static function getCodeMappingConfigName(): string
    {
        return 'shipstation_code_mapping';
    }

    protected function getCodeMappingTitleLabel(): string
    {
        return 'SkinActAftership code mapping';
    }

    protected function getCodeMappingViewWidgetClass(): string
    {
        return CodeMapping::class;
    }

    protected function getCodeMappingWeight(): int
    {
        return 300;
    }
}