<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\Traits;

trait MagicImagesTrait
{
    protected function getTargetController(): string
    {
        return 'magic_swatches_set';
    }

    protected function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActMagicImages';
    }
}