<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Traits;

use XLite\Core\Cache\ExecuteCachedTrait;

trait KlarnaTrait
{
    use ExecuteCachedTrait;

    protected function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActKlarna';
    }

    protected function getTestModeName(): string
    {
        return 'playground';
    }

    protected function getLiveModeName(): string
    {
        return 'production';
    }
}