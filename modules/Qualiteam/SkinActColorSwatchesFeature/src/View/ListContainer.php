<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\View;

use Qualiteam\SkinActColorSwatchesFeature\Traits\ColorSwatchesTrait;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ListContainer extends \XLite\View\ListContainer
{
    use ColorSwatchesTrait;

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getModulePath() . '/color_swatches.less';

        return $list;
    }
}
