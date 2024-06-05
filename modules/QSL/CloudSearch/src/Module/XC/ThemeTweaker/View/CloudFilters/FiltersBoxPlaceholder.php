<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Module\XC\ThemeTweaker\View\CloudFilters;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ThemeTweaker")
 */
class FiltersBoxPlaceholder extends \QSL\CloudSearch\View\CloudFilters\FiltersBoxPlaceholder implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    protected function getDefaultDisplayName()
    {
        return static::t('Filters');
    }

    protected function getDefaultLayoutSettingsLink(): string
    {
        return json_encode(['target' => 'module', 'moduleId' => 'QSL-CloudSearch']);
    }

    protected function getDefaultLayoutLazyLoad(): bool
    {
        return true;
    }

    protected function getIsReloadedWidget(): bool
    {
        return false;
    }
}
